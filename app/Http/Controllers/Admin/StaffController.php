<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function dashboard()
    {
        $totalRooms = Room::count();
        $todayCheckins = Booking::whereDate('check_in_date', today())->count();
        $todayCheckouts = Booking::whereDate('check_out_date', today())->count();
        $pendingServices = 0; // You can implement service requests
        
        $todaySchedule = Booking::whereDate('check_in_date', today())
            ->orWhereDate('check_out_date', today())
            ->with('guest', 'room')
            ->get();
        
        $recentBookings = Booking::latest()
            ->with('guest', 'room')
            ->take(10)
            ->get();
        
        return view('admin.staff.dashboard', compact(
            'totalRooms', 'todayCheckins', 'todayCheckouts', 'pendingServices',
            'todaySchedule', 'recentBookings'
        ));
    }
}