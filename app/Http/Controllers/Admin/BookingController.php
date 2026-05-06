<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['guest', 'room.roomType'])
            ->latest()
            ->paginate(20);
        
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('status', 'checked_out')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];
        
        return view('admin.bookings.index', compact('bookings', 'stats'));
    }
    
    public function show($id)
    {
        $booking = Booking::with(['guest', 'room.roomType'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();
        
        return response()->json(['success' => true]);
    }
    
    public function checkIn($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'checked_in';
        $booking->save();
        
        // Update room status
        $room = $booking->room;
        $room->status = 'occupied';
        $room->save();
        
        return redirect()->back()->with('success', 'Guest checked in successfully');
    }
    
    public function checkOut($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'checked_out';
        $booking->save();
        
        // Update room status
        $room = $booking->room;
        $room->status = 'available';
        $room->save();
        
        return redirect()->back()->with('success', 'Guest checked out successfully');
    }
}