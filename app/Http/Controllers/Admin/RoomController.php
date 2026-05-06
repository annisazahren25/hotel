<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms.
     */
    public function index()
    {
        try {
            $rooms = Room::with('roomType')->latest()->get();
            $roomTypes = RoomType::all();
            
            // Statistics
            $stats = [
                'total' => Room::count(),
                'available' => Room::where('status', 'available')->count(),
                'occupied' => Room::where('status', 'occupied')->count(),
                'maintenance' => Room::where('status', 'maintenance')->count(),
            ];
            
            // Floor distribution
            $floorStats = [];
            for ($floor = 1; $floor <= 8; $floor++) {
                $floorStats[$floor] = Room::where('floor', $floor)->count();
            }
            
            return view('admin.rooms.index', compact('rooms', 'roomTypes', 'stats', 'floorStats'));
            
        } catch (\Exception $e) {
            Log::error('Error loading rooms: ' . $e->getMessage());
            return view('admin.rooms.index')->with('error', 'Failed to load rooms: ' . $e->getMessage());
        }
    }
    
    /**
     * Show form to create new room.
     */
    public function create()
    {
        $roomTypes = RoomType::all();
        
        if ($roomTypes->isEmpty()) {
            return redirect()->route('admin.room-types.create')
                ->with('error', 'Please create a room type first before adding rooms.');
        }
        
        return view('admin.rooms.create', compact('roomTypes'));
    }
    
    /**
     * Store a new room.
     */
    public function store(Request $request)
    {
        try {
            // Validasi tanpa photo (foto akan diambil dari room_type)
            $validated = $request->validate([
                'room_type_id' => 'required|exists:room_types,id',
                'room_number' => 'required|string|unique:rooms,room_number|max:10',
                'floor' => 'required|integer|min:1|max:8',
                'status' => 'required|in:available,occupied,maintenance'
            ], [
                'floor.min' => 'Lantai minimal adalah 1',
                'floor.max' => 'Lantai maksimal adalah 8 (sesuai gedung hotel)',
                'room_number.unique' => 'Nomor kamar sudah terdaftar',
                'room_type_id.required' => 'Pilih tipe kamar',
                'status.required' => 'Pilih status kamar'
            ]);
            
            $data = $request->all();
            
            $room = Room::create($data);
            
            Log::info('Room created successfully', ['room_id' => $room->id, 'room_number' => $room->room_number]);
            
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room ' . $room->room_number . ' created successfully!');
                
        } catch (\Exception $e) {
            Log::error('Room creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to create room: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show form to edit room.
     */
    public function edit($id)
    {
        try {
            Log::info('Editing room ID: ' . $id);
            
            $room = Room::with('roomType')->find($id);
            
            if (!$room) {
                Log::error('Room not found with ID: ' . $id);
                return redirect()->route('admin.rooms.index')
                    ->with('error', 'Room not found! ID: ' . $id);
            }
            
            $roomTypes = RoomType::all();
            
            if ($roomTypes->isEmpty()) {
                return redirect()->route('admin.room-types.create')
                    ->with('error', 'Please create a room type first.');
            }
            
            return view('admin.rooms.edit', compact('room', 'roomTypes'));
            
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }
    
    /**
     * Update a room.
     */
    public function update(Request $request, $id)
    {
        try {
            $room = Room::findOrFail($id);
            
            // Validasi tanpa photo
            $validated = $request->validate([
                'room_type_id' => 'required|exists:room_types,id',
                'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $id,
                'floor' => 'required|integer|min:1|max:8',
                'status' => 'required|in:available,occupied,maintenance'
            ], [
                'floor.min' => 'Lantai minimal adalah 1',
                'floor.max' => 'Lantai maksimal adalah 8 (sesuai gedung hotel)',
                'room_number.unique' => 'Nomor kamar sudah terdaftar',
                'room_type_id.required' => 'Pilih tipe kamar',
                'status.required' => 'Pilih status kamar'
            ]);
            
            $room->update($request->all());
            
            Log::info('Room updated successfully', ['room_id' => $room->id, 'room_number' => $room->room_number]);
            
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room ' . $room->room_number . ' updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Room update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update room: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a room.
     */
    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $roomNumber = $room->room_number;
            
            // Check if room has active bookings
            $activeBookings = $room->bookings()
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->exists();
                
            if ($activeBookings) {
                return redirect()->route('admin.rooms.index')
                    ->with('error', 'Cannot delete room ' . $roomNumber . ' because it has active bookings!');
            }
            
            $room->delete();
            
            Log::info('Room deleted successfully', ['room_id' => $id, 'room_number' => $roomNumber]);
            
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room ' . $roomNumber . ' deleted successfully!');
                
        } catch (\Exception $e) {
            Log::error('Room deletion failed: ' . $e->getMessage());
            
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Failed to delete room: ' . $e->getMessage());
        }
    }
    
    /**
     * Update room status (AJAX).
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:available,occupied,maintenance'
            ]);
            
            $room = Room::findOrFail($id);
            $oldStatus = $room->status;
            $room->status = $request->status;
            $room->save();
            
            $statusMessages = [
                'available' => 'available for booking',
                'occupied' => 'occupied by guest',
                'maintenance' => 'under maintenance'
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Room #' . $room->room_number . ' is now ' . $statusMessages[$request->status],
                'status' => $room->status,
                'room_number' => $room->room_number,
                'old_status' => $oldStatus
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get rooms by floor.
     */
    public function getRoomsByFloor($floor)
    {
        try {
            $floor = (int)$floor;
            
            if ($floor < 1 || $floor > 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'Floor must be between 1 and 8'
                ], 400);
            }
            
            $rooms = Room::with('roomType')
                ->where('floor', $floor)
                ->get();
            
            return response()->json([
                'success' => true,
                'floor' => $floor,
                'total' => $rooms->count(),
                'rooms' => $rooms->map(function($room) {
                    return [
                        'id' => $room->id,
                        'room_number' => $room->room_number,
                        'status' => $room->status,
                        'type' => $room->roomType->name ?? 'Standard',
                        'price' => $room->roomType->price ?? 0
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get available rooms.
     */
    public function getAvailableRooms(Request $request)
    {
        try {
            $query = Room::with('roomType')->where('status', 'available');
            
            if ($request->has('floor')) {
                $query->where('floor', $request->floor);
            }
            
            if ($request->has('room_type_id')) {
                $query->where('room_type_id', $request->room_type_id);
            }
            
            $rooms = $query->get();
            
            return response()->json([
                'success' => true,
                'count' => $rooms->count(),
                'rooms' => $rooms
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk update room status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'room_ids' => 'required|array',
                'room_ids.*' => 'exists:rooms,id',
                'status' => 'required|in:available,occupied,maintenance'
            ]);
            
            $updated = Room::whereIn('id', $request->room_ids)
                ->update(['status' => $request->status]);
            
            return redirect()->route('admin.rooms.index')
                ->with('success', $updated . ' rooms status updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Failed to update rooms: ' . $e->getMessage());
        }
    }
    
    /**
     * Export rooms data to CSV.
     */
    public function export()
    {
        try {
            $rooms = Room::with('roomType')->get();
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="rooms_export_' . date('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($rooms) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for special characters
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Add headers
                fputcsv($file, ['ID', 'Room Number', 'Floor', 'Room Type', 'Price', 'Status', 'Created At']);
                
                // Add data
                foreach ($rooms as $room) {
                    fputcsv($file, [
                        $room->id,
                        $room->room_number,
                        $room->floor,
                        $room->roomType->name ?? '-',
                        number_format($room->roomType->price ?? 0, 0, ',', '.'),
                        $room->status,
                        $room->created_at ? $room->created_at->format('Y-m-d H:i:s') : ''
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Failed to export: ' . $e->getMessage());
        }
    }
    
    /**
     * Get floor statistics.
     */
    public function getFloorStats()
    {
        try {
            $stats = [];
            
            for ($floor = 1; $floor <= 8; $floor++) {
                $total = Room::where('floor', $floor)->count();
                $available = Room::where('floor', $floor)->where('status', 'available')->count();
                $occupied = Room::where('floor', $floor)->where('status', 'occupied')->count();
                $maintenance = Room::where('floor', $floor)->where('status', 'maintenance')->count();
                
                $stats[$floor] = [
                    'floor' => $floor,
                    'total' => $total,
                    'available' => $available,
                    'occupied' => $occupied,
                    'maintenance' => $maintenance,
                    'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0
                ];
            }
            
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
    
    /**
     * Get room details for AJAX.
     */
    public function getRoomDetails($id)
    {
        try {
            $room = Room::with(['roomType', 'bookings' => function($query) {
                $query->whereIn('status', ['confirmed', 'checked_in'])
                      ->with('guest')
                      ->latest()
                      ->take(1);
            }])->find($id);
            
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }
            
            $currentBooking = $room->bookings ? $room->bookings->first() : null;
            
            return response()->json([
                'success' => true,
                'room' => [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'floor' => $room->floor,
                    'status' => $room->status,
                    'type' => $room->roomType ? $room->roomType->name : 'N/A',
                    'price' => $room->roomType ? $room->roomType->price : 0,
                    'photo_url' => $room->roomType ? $room->roomType->photo_url : null,
                    'description' => $room->roomType ? $room->roomType->description : '',
                    'capacity' => $room->roomType ? $room->roomType->capacity : 2,
                    'current_booking' => $currentBooking ? [
                        'guest_name' => $currentBooking->guest ? $currentBooking->guest->name : 'N/A',
                        'check_in' => $currentBooking->check_in_date,
                        'check_out' => $currentBooking->check_out_date
                    ] : null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get room details failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load room details'
            ], 500);
        }
    }
    
    /**
     * Clone room (duplicate).
     */
    public function clone($id)
    {
        try {
            $room = Room::findOrFail($id);
            
            // Generate new room number
            $lastRoom = Room::orderBy('id', 'desc')->first();
            $newRoomNumber = $lastRoom ? (int)$lastRoom->room_number + 1 : 100;
            
            $newRoom = $room->replicate();
            $newRoom->room_number = (string)$newRoomNumber;
            $newRoom->status = 'available';
            $newRoom->save();
            
            Log::info('Room cloned successfully', [
                'original_room' => $room->id,
                'new_room' => $newRoom->id,
                'new_room_number' => $newRoomNumber
            ]);
            
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room cloned successfully! New room number: ' . $newRoomNumber);
                
        } catch (\Exception $e) {
            Log::error('Clone failed: ' . $e->getMessage());
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Failed to clone room: ' . $e->getMessage());
        }
    }
    
    /**
     * Search rooms.
     */
    public function search(Request $request)
    {
        try {
            $query = Room::with('roomType');
            
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('room_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('roomType', function($q2) use ($search) {
                          $q2->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }
            
            if ($request->has('floor') && $request->floor) {
                $query->where('floor', $request->floor);
            }
            
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            $rooms = $query->get();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'count' => $rooms->count(),
                    'rooms' => $rooms
                ]);
            }
            
            return view('admin.rooms.index', compact('rooms'));
            
        } catch (\Exception $e) {
            Log::error('Search failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.rooms.index')
                ->with('error', 'Search failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get room statistics for dashboard.
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Room::count(),
                'available' => Room::where('status', 'available')->count(),
                'occupied' => Room::where('status', 'occupied')->count(),
                'maintenance' => Room::where('status', 'maintenance')->count(),
                'by_floor' => []
            ];
            
            for ($floor = 1; $floor <= 8; $floor++) {
                $stats['by_floor'][$floor] = [
                    'total' => Room::where('floor', $floor)->count(),
                    'available' => Room::where('floor', $floor)->where('status', 'available')->count(),
                    'occupied' => Room::where('floor', $floor)->where('status', 'occupied')->count(),
                ];
            }
            
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