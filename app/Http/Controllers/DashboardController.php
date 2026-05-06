<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Guest;
use App\Models\RestaurantOrder;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total counts
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();
        
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $checkedInBookings = Booking::where('status', 'checked_in')->count();
        
        $totalGuests = Guest::where('role', 'customer')->count();
        $totalStaff = Guest::whereIn('role', ['admin', 'staff', 'housekeeping', 'restaurant'])->count();
        
        $totalRestaurantOrders = RestaurantOrder::count();
        $pendingOrders = RestaurantOrder::where('status', 'ordered')->count();
        
        // Revenue
        $todayRevenue = Payment::whereDate('created_at', today())->sum('amount');
        $weekRevenue = Payment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
        $monthRevenue = Payment::whereMonth('created_at', now()->month)->sum('amount');
        $totalRevenue = Payment::sum('amount');
        
        // Recent bookings
        $recentBookings = Booking::with(['guest', 'room.roomType'])
            ->latest()
            ->take(10)
            ->get();
        
        // Recent orders
        $recentOrders = RestaurantOrder::with('guest')
            ->latest()
            ->take(10)
            ->get();
        
        // Chart data - bookings per month
        $bookingsPerMonth = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('count', 'month')
        ->toArray();
        
        $monthlyBookings = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyBookings[] = $bookingsPerMonth[$i] ?? 0;
        }
        
        // Chart data - revenue per month
        $revenuePerMonth = Payment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount) as total')
        )
        ->whereYear('created_at', now()->year)
        ->where('payment_status', 'paid')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('total', 'month')
        ->toArray();
        
        $monthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[] = $revenuePerMonth[$i] ?? 0;
        }
        
        // Occupancy rate
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        
        // Room status distribution
        $roomStatus = [
            'available' => $availableRooms,
            'occupied' => $occupiedRooms,
            'maintenance' => $maintenanceRooms,
        ];
        
        return view('admin.dashboard.index', compact(
            'totalRooms', 'availableRooms', 'occupiedRooms', 'maintenanceRooms',
            'totalBookings', 'pendingBookings', 'confirmedBookings', 'checkedInBookings',
            'totalGuests', 'totalStaff',
            'totalRestaurantOrders', 'pendingOrders',
            'todayRevenue', 'weekRevenue', 'monthRevenue', 'totalRevenue',
            'recentBookings', 'recentOrders',
            'monthlyBookings', 'monthlyRevenue',
            'occupancyRate', 'roomStatus'
        ));
    }
}