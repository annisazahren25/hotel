<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantOrder;
use App\Models\RestaurantMenu;
use App\Models\RestaurantOrderItem;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    /**
     * Dashboard for restaurant management.
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total_orders' => RestaurantOrder::count(),
                'pending_orders' => RestaurantOrder::where('status', 'ordered')->count(),
                'preparing_orders' => RestaurantOrder::where('status', 'preparing')->count(),
                'ready_orders' => RestaurantOrder::where('status', 'ready')->count(),
                'delivered_orders' => RestaurantOrder::where('status', 'delivered')->count(),
                'paid_orders' => RestaurantOrder::where('status', 'paid')->count(),
                'cancelled_orders' => RestaurantOrder::where('status', 'cancelled')->count(),
                'total_revenue' => RestaurantOrder::where('status', 'paid')->sum('total_price'),
                'today_revenue' => RestaurantOrder::whereDate('created_at', today())->where('status', 'paid')->sum('total_price'),
                'weekly_revenue' => RestaurantOrder::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'paid')->sum('total_price'),
                'monthly_revenue' => RestaurantOrder::whereMonth('created_at', now()->month)->where('status', 'paid')->sum('total_price'),
            ];
            
            $recentOrders = RestaurantOrder::with('guest')
                ->latest()
                ->take(10)
                ->get();
            
            $popularItems = RestaurantOrderItem::select('menu_id', DB::raw('SUM(quantity) as total_sold'))
                ->with('menu')
                ->groupBy('menu_id')
                ->orderBy('total_sold', 'desc')
                ->take(5)
                ->get();
            
            $orderTypeStats = [
                'dine_in' => RestaurantOrder::where('order_type', 'dine_in')->count(),
                'room_delivery' => RestaurantOrder::where('order_type', 'room_delivery')->count(),
            ];
            
            return view('admin.restaurant.dashboard', compact('stats', 'recentOrders', 'popularItems', 'orderTypeStats'));
            
        } catch (\Exception $e) {
            Log::error('Restaurant dashboard error: ' . $e->getMessage());
            return view('admin.restaurant.dashboard')->with('error', 'Failed to load dashboard: ' . $e->getMessage());
        }
    }
    
    /**
     * Display list of restaurant orders.
     */
    public function orders(Request $request)
    {
        try {
            $query = RestaurantOrder::with('guest', 'items.menu');
            
            // Filter by status
            if ($request->has('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }
            
            // Filter by order type
            if ($request->has('order_type') && $request->order_type != 'all') {
                $query->where('order_type', $request->order_type);
            }
            
            // Filter by date range
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            // Search by guest name or order ID
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id', 'LIKE', "%{$search}%")
                      ->orWhereHas('guest', function($q2) use ($search) {
                          $q2->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }
            
            $orders = $query->latest()->paginate(20);
            
            $stats = [
                'total' => RestaurantOrder::count(),
                'ordered' => RestaurantOrder::where('status', 'ordered')->count(),
                'preparing' => RestaurantOrder::where('status', 'preparing')->count(),
                'ready' => RestaurantOrder::where('status', 'ready')->count(),
                'delivered' => RestaurantOrder::where('status', 'delivered')->count(),
                'paid' => RestaurantOrder::where('status', 'paid')->count(),
                'cancelled' => RestaurantOrder::where('status', 'cancelled')->count(),
                'dine_in' => RestaurantOrder::where('order_type', 'dine_in')->count(),
                'room_delivery' => RestaurantOrder::where('order_type', 'room_delivery')->count(),
            ];
            
            return view('admin.restaurant.orders', compact('orders', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Orders page error: ' . $e->getMessage());
            return redirect()->route('admin.restaurant.dashboard')
                ->with('error', 'Failed to load orders: ' . $e->getMessage());
        }
    }
    
    /**
     * Display order details.
     */
    public function orderDetail($id)
    {
        try {
            $order = RestaurantOrder::with(['guest', 'items.menu'])
                ->findOrFail($id);
            
            // Get related orders from same guest
            $relatedOrders = RestaurantOrder::where('guest_id', $order->guest_id)
                ->where('id', '!=', $id)
                ->latest()
                ->take(5)
                ->get();
            
            return view('admin.restaurant.order-detail', compact('order', 'relatedOrders'));
            
        } catch (\Exception $e) {
            Log::error('Order detail error: ' . $e->getMessage());
            return redirect()->route('admin.restaurant.orders')
                ->with('error', 'Order not found!');
        }
    }
    
    /**
     * Update order status via AJAX.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:ordered,preparing,ready,delivered,paid,cancelled'
            ]);
            
            $order = RestaurantOrder::findOrFail($id);
            $oldStatus = $order->status;
            $order->status = $request->status;
            
            // If status is delivered or paid, record completion time
            if (in_array($request->status, ['delivered', 'paid'])) {
                $order->completed_at = now();
            }
            
            $order->save();
            
            $statusMessages = [
                'ordered' => 'Order has been placed and is pending',
                'preparing' => 'Order is now being prepared in the kitchen',
                'ready' => 'Order is ready for pickup or delivery',
                'delivered' => 'Order has been delivered to the guest',
                'paid' => 'Payment has been completed for this order',
                'cancelled' => 'Order has been cancelled'
            ];
            
            return response()->json([
                'success' => true,
                'message' => $statusMessages[$request->status],
                'status' => $order->status,
                'order_id' => $order->id,
                'old_status' => $oldStatus
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk update order statuses.
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'order_ids' => 'required|array',
                'order_ids.*' => 'exists:restaurant_orders,id',
                'status' => 'required|in:ordered,preparing,ready,delivered,paid,cancelled'
            ]);
            
            $updated = RestaurantOrder::whereIn('id', $request->order_ids)
                ->update(['status' => $request->status]);
            
            Log::info('Bulk order status updated', [
                'order_ids' => $request->order_ids,
                'new_status' => $request->status,
                'count' => $updated
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "{$updated} orders updated to " . $request->status,
                'count' => $updated
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update orders: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display menu management page.
     */
    public function menu()
    {
        try {
            $menus = RestaurantMenu::orderBy('category')->orderBy('name')->get();
            
            $categories = RestaurantMenu::select('category')
                ->distinct()
                ->pluck('category');
            
            $stats = [
                'total_items' => RestaurantMenu::count(),
                'available_items' => RestaurantMenu::where('is_available', true)->count(),
                'unavailable_items' => RestaurantMenu::where('is_available', false)->count(),
                'average_price' => RestaurantMenu::avg('price'),
                'total_orders' => RestaurantOrder::count(),
            ];
            
            return view('admin.restaurant.menu', compact('menus', 'categories', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Menu page error: ' . $e->getMessage());
            return view('admin.restaurant.menu')->with('error', 'Failed to load menu: ' . $e->getMessage());
        }
    }
    
    /**
     * Store new menu item.
     */
    public function storeMenu(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'is_available' => 'nullable|boolean',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
            ], [
                'name.required' => 'Nama menu wajib diisi',
                'price.required' => 'Harga wajib diisi',
                'price.numeric' => 'Harga harus berupa angka',
                'price.min' => 'Harga minimal 0',
                'category.required' => 'Kategori wajib diisi',
                'photo.image' => 'File harus berupa gambar',
                'photo.mimes' => 'Format gambar harus: JPG, PNG, GIF, WEBP',
                'photo.max' => 'Ukuran gambar maksimal 10MB'
            ]);
            
            $data = $request->except('photo');
            $data['is_available'] = $request->has('is_available');
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . Str::slug($request->name) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('menu', $filename, 'public');
                $data['photo_url'] = '/storage/' . $path;
            }
            
            $menu = RestaurantMenu::create($data);
            
            Log::info('Menu item created', [
                'id' => $menu->id, 
                'name' => $menu->name,
                'price' => $menu->price,
                'category' => $menu->category
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Menu item "' . $menu->name . '" created successfully!',
                    'menu' => $menu
                ]);
            }
            
            return redirect()->route('admin.restaurant.menu')
                ->with('success', 'Menu item "' . $menu->name . '" created successfully!');
                
        } catch (\Exception $e) {
            Log::error('Menu creation failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create menu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to create menu: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update menu item.
     */
    public function updateMenu(Request $request, $id)
    {
        try {
            $menu = RestaurantMenu::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'is_available' => 'nullable|boolean',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
            ]);
            
            $data = $request->except('photo', '_method', '_token');
            $data['is_available'] = $request->has('is_available');
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($menu->photo_url && file_exists(public_path($menu->photo_url))) {
                    unlink(public_path($menu->photo_url));
                }
                
                $photo = $request->file('photo');
                $filename = time() . '_' . Str::slug($request->name) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('menu', $filename, 'public');
                $data['photo_url'] = '/storage/' . $path;
            }
            
            // Handle remove photo
            if ($request->has('remove_photo') && $request->remove_photo == 1) {
                if ($menu->photo_url && file_exists(public_path($menu->photo_url))) {
                    unlink(public_path($menu->photo_url));
                }
                $data['photo_url'] = null;
            }
            
            $menu->update($data);
            
            Log::info('Menu item updated', [
                'id' => $menu->id, 
                'name' => $menu->name,
                'price' => $menu->price
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Menu item "' . $menu->name . '" updated successfully!',
                    'menu' => $menu
                ]);
            }
            
            return redirect()->route('admin.restaurant.menu')
                ->with('success', 'Menu item "' . $menu->name . '" updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Menu update failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update menu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to update menu: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Toggle menu availability.
     */
    public function toggleAvailability($id)
    {
        try {
            $menu = RestaurantMenu::findOrFail($id);
            $menu->is_available = !$menu->is_available;
            $menu->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item "' . $menu->name . '" is now ' . ($menu->is_available ? 'available' : 'unavailable'),
                'is_available' => $menu->is_available
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle availability: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete menu item.
     */
    public function destroyMenu($id)
    {
        try {
            $menu = RestaurantMenu::findOrFail($id);
            $name = $menu->name;
            
            // Check if menu item has been ordered
            $hasOrders = RestaurantOrderItem::where('menu_id', $id)->exists();
            if ($hasOrders) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete "' . $name . '" because it has been ordered before!'
                    ], 400);
                }
                return redirect()->route('admin.restaurant.menu')
                    ->with('error', 'Cannot delete "' . $name . '" because it has been ordered before!');
            }
            
            // Delete photo
            if ($menu->photo_url && file_exists(public_path($menu->photo_url))) {
                unlink(public_path($menu->photo_url));
            }
            
            $menu->delete();
            
            Log::info('Menu item deleted', ['id' => $id, 'name' => $name]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Menu item "' . $name . '" deleted successfully!'
                ]);
            }
            
            return redirect()->route('admin.restaurant.menu')
                ->with('success', 'Menu item "' . $name . '" deleted successfully!');
                
        } catch (\Exception $e) {
            Log::error('Menu deletion failed: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete menu: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.restaurant.menu')
                ->with('error', 'Failed to delete menu: ' . $e->getMessage());
        }
    }
    
    /**
     * Export orders to CSV.
     */
    public function exportOrders()
    {
        try {
            $orders = RestaurantOrder::with('guest')->get();
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="restaurant_orders_' . date('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($orders) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, ['ID', 'Guest Name', 'Order Type', 'Subtotal', 'Tax (12%)', 'Delivery Fee', 'Total', 'Status', 'Created At']);
                
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->id,
                        $order->guest->name ?? 'N/A',
                        $order->order_type == 'dine_in' ? 'Dine In' : 'Room Delivery',
                        number_format($order->subtotal, 0, ',', '.'),
                        number_format($order->tax, 0, ',', '.'),
                        number_format($order->delivery_fee, 0, ',', '.'),
                        number_format($order->total_price, 0, ',', '.'),
                        $order->status,
                        $order->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export');
        }
    }
    
    /**
     * Get order statistics for dashboard (AJAX).
     */
    public function getStatistics()
    {
        try {
            $dailyOrders = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dailyOrders[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('D'),
                    'orders' => RestaurantOrder::whereDate('created_at', $date)->count(),
                    'revenue' => RestaurantOrder::whereDate('created_at', $date)->where('status', 'paid')->sum('total_price'),
                ];
            }
            
            $stats = [
                'today' => [
                    'orders' => RestaurantOrder::whereDate('created_at', today())->count(),
                    'revenue' => RestaurantOrder::whereDate('created_at', today())->where('status', 'paid')->sum('total_price'),
                ],
                'week' => [
                    'orders' => RestaurantOrder::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'revenue' => RestaurantOrder::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'paid')->sum('total_price'),
                ],
                'month' => [
                    'orders' => RestaurantOrder::whereMonth('created_at', now()->month)->count(),
                    'revenue' => RestaurantOrder::whereMonth('created_at', now()->month)->where('status', 'paid')->sum('total_price'),
                ],
                'by_type' => [
                    'dine_in' => RestaurantOrder::where('order_type', 'dine_in')->count(),
                    'room_delivery' => RestaurantOrder::where('order_type', 'room_delivery')->count(),
                ],
                'by_status' => [
                    'ordered' => RestaurantOrder::where('status', 'ordered')->count(),
                    'preparing' => RestaurantOrder::where('status', 'preparing')->count(),
                    'ready' => RestaurantOrder::where('status', 'ready')->count(),
                    'delivered' => RestaurantOrder::where('status', 'delivered')->count(),
                    'paid' => RestaurantOrder::where('status', 'paid')->count(),
                    'cancelled' => RestaurantOrder::where('status', 'cancelled')->count(),
                ],
                'daily_orders' => $dailyOrders
            ];
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}