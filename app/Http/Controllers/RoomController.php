<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::with(['rooms' => function($q) {
            $q->where('status', 'available');
        }])->get();
        
        return view('rooms.index', compact('roomTypes'));
    }
    
    /**
     * Check room availability based on dates and guests
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:10'
        ]);
        
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $guests = $request->guests;
        
        // Get all room types with capacity >= guests
        $roomTypes = RoomType::where('capacity', '>=', $guests)->get();
        
        $availableRooms = [];
        
        foreach ($roomTypes as $roomType) {
            // Get rooms of this type that are available
            $rooms = Room::where('room_type_id', $roomType->id)
                ->where('status', 'available')
                ->get();
            
            $availableCount = 0;
            $roomList = [];
            
            foreach ($rooms as $room) {
                // Check if room is not booked for the selected dates
                $isBooked = Booking::where('room_id', $room->id)
                    ->where('status', '!=', 'cancelled')
                    ->where('status', '!=', 'checked_out')
                    ->where(function($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                          ->orWhere(function($q2) use ($checkIn, $checkOut) {
                              $q2->where('check_in_date', '<=', $checkIn)
                                 ->where('check_out_date', '>=', $checkOut);
                          });
                    })
                    ->exists();
                
                if (!$isBooked) {
                    $availableCount++;
                    $roomList[] = $room;
                }
            }
            
            if ($availableCount > 0) {
                $availableRooms[] = [
                    'room_type' => $roomType,
                    'available_count' => $availableCount,
                    'rooms' => $roomList,
                    'price_per_night' => $roomType->price,
                    'total_nights' => \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut),
                    'total_price' => $roomType->price * \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut)
                ];
            }
        }
        
        // Store search parameters in session for booking
        session()->put('search', [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $guests
        ]);
        
        return view('rooms.available', compact('availableRooms', 'checkIn', 'checkOut', 'guests'));
    }
    
    public function detail($id)
    {
        $roomType = RoomType::with('rooms')->findOrFail($id);
        return view('rooms.detail', compact('roomType'));
    }
}