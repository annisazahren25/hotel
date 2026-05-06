<?php

namespace App\Http\Controllers;

use App\Models\RestaurantMenu;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RestaurantController extends Controller
{
    /**
     * Display restaurant menu for customers.
     */
    public function menu()
    {
        $menus = RestaurantMenu::where('is_available', true)->orderBy('category')->get();
        $bookings = Auth::check() ? Auth::user()->bookings()->where('status', 'checked_in')->get() : collect();
        $cart = session()->get('cart', []);
        
        // Hitung total quantity (bukan jumlah jenis menu)
        $cartCount = 0;
        foreach($cart as $item) {
            $cartCount += $item['quantity'];
        }
        
        return view('restaurant.menu', compact('menus', 'bookings', 'cartCount'));
    }
    
    /**
     * View cart.
     */
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $totalQuantity = 0;
        
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }
        
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;
        
        return view('restaurant.cart', compact('cart', 'subtotal', 'tax', 'total', 'totalQuantity'));
    }
    
    /**
     * Add item to cart.
     */
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'menu_id' => 'required|exists:restaurant_menu,id',
                'quantity' => 'required|integer|min:1|max:10'
            ]);
            
            $cart = session()->get('cart', []);
            $menu = RestaurantMenu::findOrFail($request->menu_id);
            
            if(isset($cart[$request->menu_id])) {
                $cart[$request->menu_id]['quantity'] += $request->quantity;
            } else {
                $cart[$request->menu_id] = [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $request->quantity,
                    'image' => $menu->photo_url ?? null
                ];
            }
            
            session()->put('cart', $cart);
            
            // Hitung total quantity
            $totalQuantity = 0;
            foreach($cart as $item) {
                $totalQuantity += $item['quantity'];
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => $menu->name . ' added to cart!',
                    'cart_count' => $totalQuantity
                ]);
            }
            
            return redirect()->back()->with('success', $menu->name . ' added to cart!');
            
        } catch (\Exception $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to add item to cart: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to add item to cart');
        }
    }
    
    /**
     * Remove item from cart.
     */
    public function removeFromCart($id)
    {
        try {
            $cart = session()->get('cart', []);
            
            if(isset($cart[$id])) {
                $itemName = $cart[$id]['name'];
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
            
            return redirect()->route('cart.view')->with('success', 'Item removed from cart');
            
        } catch (\Exception $e) {
            Log::error('Remove from cart error: ' . $e->getMessage());
            return redirect()->route('cart.view')->with('error', 'Failed to remove item');
        }
    }
    
    /**
     * Update cart item quantity.
     */
    public function updateCart(Request $request)
    {
        try {
            $request->validate([
                'menu_id' => 'required',
                'quantity' => 'required|integer|min:0|max:10'
            ]);
            
            $cart = session()->get('cart', []);
            
            if(isset($cart[$request->menu_id])) {
                if($request->quantity <= 0) {
                    unset($cart[$request->menu_id]);
                } else {
                    $cart[$request->menu_id]['quantity'] = $request->quantity;
                }
                session()->put('cart', $cart);
            }
            
            return redirect()->route('cart.view')->with('success', 'Cart updated successfully');
            
        } catch (\Exception $e) {
            Log::error('Update cart error: ' . $e->getMessage());
            return redirect()->route('cart.view')->with('error', 'Failed to update cart');
        }
    }
    
    /**
     * Get cart count for AJAX.
     */
    public function getCartCount()
    {
        $cart = session()->get('cart', []);
        
        // Hitung total quantity
        $totalQuantity = 0;
        foreach($cart as $item) {
            $totalQuantity += $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'count' => $totalQuantity
        ]);
    }
    
    /**
     * Process checkout and create order.
     */
    public function checkout(Request $request)
    {
        try {
            $cart = session()->get('cart');
            
            if(!$cart || count($cart) == 0) {
                return redirect()->route('cart.view')->with('error', 'Your cart is empty');
            }
            
            $request->validate([
                'room_number' => 'nullable|string',
                'special_requests' => 'nullable|string'
            ]);
            
            $subtotal = 0;
            foreach($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;
            
            // Generate unique order number
            $orderNumber = 'ORD-' . strtoupper(uniqid());
            
            // Find booking if room number is provided
            $bookingId = null;
            if ($request->room_number) {
                $booking = Booking::whereHas('room', function($q) use ($request) {
                    $q->where('room_number', $request->room_number);
                })
                ->where('guest_id', Auth::id())
                ->where('status', 'checked_in')
                ->first();
                
                if ($booking) {
                    $bookingId = $booking->id;
                }
            }
            
            // Create order
            $order = RestaurantOrder::create([
                'guest_id' => Auth::id(),
                'booking_id' => $bookingId,
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_price' => $total,
                'room_number' => $request->room_number,
                'special_requests' => $request->special_requests,
                'status' => 'pending'
            ]);
            
            // Create order items
            foreach($cart as $menuId => $item) {
                RestaurantOrderItem::create([
                    'restaurant_order_id' => $order->id,
                    'menu_id' => $menuId,
                    'menu_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }
            
            // Clear cart
            session()->forget('cart');
            
            Log::info('New restaurant order created', [
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'total' => $total
            ]);
            
            // Redirect to payment page
            return redirect()->route('payment.restaurant', $order->id)
                ->with('success', 'Order placed successfully! Please complete payment.');
                
        } catch (\Exception $e) {
            Log::error('Checkout failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }
    
    /**
     * Display customer's orders.
     */
    public function myOrders()
    {
        try {
            $orders = RestaurantOrder::where('guest_id', Auth::id())
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return view('restaurant.orders', compact('orders'));
            
        } catch (\Exception $e) {
            Log::error('My orders error: ' . $e->getMessage());
            return view('restaurant.orders')->with('orders', collect())->with('error', 'Failed to load orders');
        }
    }
    
    /**
     * Display order details for customer.
     */
    public function orderDetail($id)
    {
        try {
            $order = RestaurantOrder::with('items')
                ->where('guest_id', Auth::id())
                ->findOrFail($id);
            
            return view('restaurant.order-detail', compact('order'));
            
        } catch (\Exception $e) {
            Log::error('Order detail error: ' . $e->getMessage());
            return redirect()->route('restaurant.orders')
                ->with('error', 'Order not found');
        }
    }
    
    /**
     * Track order status for customer.
     */
    public function trackOrder($id)
    {
        try {
            $order = RestaurantOrder::with('items')
                ->where('guest_id', Auth::id())
                ->findOrFail($id);
            
            // Status progress percentages
            $statusProgress = [
                'pending' => 10,
                'preparing' => 30,
                'ready' => 60,
                'delivered' => 85,
                'paid' => 100,
                'cancelled' => 0,
            ];
            
            // Status messages
            $statusMessages = [
                'pending' => 'Your order has been placed and is waiting for confirmation.',
                'preparing' => 'Your order is being prepared by our chef.',
                'ready' => 'Your order is ready for pickup or delivery!',
                'delivered' => 'Your order has been delivered. Enjoy your meal!',
                'paid' => 'Payment completed. Thank you for dining with us!',
                'cancelled' => 'This order has been cancelled.',
            ];
            
            // Estimated times
            $estimatedTimes = [
                'pending' => '5-10 minutes',
                'preparing' => '10-15 minutes',
                'ready' => 'Ready now',
                'delivered' => 'Delivered',
                'paid' => 'Completed',
                'cancelled' => 'Cancelled',
            ];
            
            $order->progress_percentage = $statusProgress[$order->status] ?? 0;
            $order->status_message = $statusMessages[$order->status] ?? '';
            $order->estimated_time = $estimatedTimes[$order->status] ?? 'Processing';
            
            return view('restaurant.track', compact('order'));
            
        } catch (\Exception $e) {
            Log::error('Track order error: ' . $e->getMessage());
            return redirect()->route('restaurant.orders')
                ->with('error', 'Order not found');
        }
    }
    
    /**
     * Get order status for AJAX polling.
     */
    public function getOrderStatus($id)
    {
        try {
            $order = RestaurantOrder::where('guest_id', Auth::id())->findOrFail($id);
            
            $statusProgress = [
                'pending' => 10,
                'preparing' => 30,
                'ready' => 60,
                'delivered' => 85,
                'paid' => 100,
                'cancelled' => 0,
            ];
            
            $statusMessages = [
                'pending' => 'Order placed',
                'preparing' => 'Being prepared',
                'ready' => 'Ready for pickup/delivery',
                'delivered' => 'Delivered',
                'paid' => 'Payment completed',
                'cancelled' => 'Cancelled',
            ];
            
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'status_text' => $statusMessages[$order->status] ?? $order->status,
                'progress' => $statusProgress[$order->status] ?? 0,
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get order status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }
    
    /**
     * Cancel order (customer).
     */
    public function cancelOrder($id)
    {
        try {
            $order = RestaurantOrder::where('guest_id', Auth::id())
                ->whereIn('status', ['pending', 'preparing'])
                ->findOrFail($id);
            
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->save();
            
            Log::info('Restaurant order cancelled', [
                'order_id' => $id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('restaurant.orders')
                ->with('success', 'Order cancelled successfully');
                
        } catch (\Exception $e) {
            Log::error('Cancel order error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Cannot cancel this order. Order may already be processed.');
        }
    }
}