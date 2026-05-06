<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of room types.
     */
    public function index()
    {
        try {
            // Get all room types with rooms count
            $roomTypes = RoomType::withCount('rooms')->orderBy('created_at', 'desc')->get();
            
            // Get statistics
            $stats = [
                'total' => $roomTypes->count(),
                'total_rooms' => $roomTypes->sum('rooms_count'),
                'avg_price' => $roomTypes->avg('price'),
                'max_price' => $roomTypes->max('price'),
                'min_price' => $roomTypes->min('price'),
            ];
            
            return view('admin.room-types.index', compact('roomTypes', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Error loading room types: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to load room types: ' . $e->getMessage());
        }
    }
    
    /**
     * Show form to create new room type.
     */
    public function create()
    {
        return view('admin.room-types.create');
    }
    
    /**
     * Store a new room type.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:room_types,name',
                'price' => 'required|numeric|min:0',
                'capacity' => 'required|integer|min:1|max:10',
                'description' => 'nullable|string|max:1000',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
            ], [
                'name.required' => 'Nama tipe kamar wajib diisi',
                'name.unique' => 'Nama tipe kamar sudah ada',
                'name.max' => 'Nama tipe kamar maksimal 255 karakter',
                'price.required' => 'Harga wajib diisi',
                'price.numeric' => 'Harga harus berupa angka',
                'price.min' => 'Harga minimal 0',
                'capacity.required' => 'Kapasitas wajib diisi',
                'capacity.integer' => 'Kapasitas harus berupa angka',
                'capacity.min' => 'Kapasitas minimal 1 orang',
                'capacity.max' => 'Kapasitas maksimal 10 orang',
                'description.max' => 'Deskripsi maksimal 1000 karakter',
                'photo.image' => 'File harus berupa gambar',
                'photo.mimes' => 'Format gambar harus: JPG, PNG, GIF, WEBP',
                'photo.max' => 'Ukuran gambar maksimal 10MB'
            ]);
            
            $data = $request->except('photo');
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . Str::slug($request->name) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('room-types', $filename, 'public');
                $data['photo_url'] = '/storage/' . $path;
            }
            
            $roomType = RoomType::create($data);
            
            Log::info('Room type created successfully', [
                'id' => $roomType->id, 
                'name' => $roomType->name,
                'price' => $roomType->price,
                'capacity' => $roomType->capacity
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Room type "' . $roomType->name . '" created successfully!',
                    'room_type' => $roomType
                ]);
            }
            
            return redirect()->route('admin.room-types.index')
                ->with('success', 'Room type "' . $roomType->name . '" created successfully!');
                
        } catch (\Exception $e) {
            Log::error('Room type creation failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create room type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to create room type: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show form to edit room type.
     */
    public function edit($id)
    {
        try {
            $roomType = RoomType::withCount('rooms')->findOrFail($id);
            
            return view('admin.room-types.edit', compact('roomType'));
            
        } catch (\Exception $e) {
            Log::error('Room type edit - not found: ' . $e->getMessage());
            return redirect()->route('admin.room-types.index')
                ->with('error', 'Room type not found!');
        }
    }
    
    /**
     * Update room type.
     */
    public function update(Request $request, $id)
    {
        try {
            $roomType = RoomType::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:room_types,name,' . $id,
                'price' => 'required|numeric|min:0',
                'capacity' => 'required|integer|min:1|max:10',
                'description' => 'nullable|string|max:1000',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'remove_photo' => 'nullable|boolean'
            ], [
                'name.required' => 'Nama tipe kamar wajib diisi',
                'name.unique' => 'Nama tipe kamar sudah ada',
                'name.max' => 'Nama tipe kamar maksimal 255 karakter',
                'price.required' => 'Harga wajib diisi',
                'price.numeric' => 'Harga harus berupa angka',
                'price.min' => 'Harga minimal 0',
                'capacity.required' => 'Kapasitas wajib diisi',
                'capacity.integer' => 'Kapasitas harus berupa angka',
                'capacity.min' => 'Kapasitas minimal 1 orang',
                'capacity.max' => 'Kapasitas maksimal 10 orang',
                'description.max' => 'Deskripsi maksimal 1000 karakter',
                'photo.image' => 'File harus berupa gambar',
                'photo.mimes' => 'Format gambar harus: JPG, PNG, GIF, WEBP',
                'photo.max' => 'Ukuran gambar maksimal 10MB'
            ]);
            
            $data = $request->except('photo', '_method', '_token', 'remove_photo');
            
            // Handle remove photo
            if ($request->has('remove_photo') && $request->remove_photo == 1) {
                if ($roomType->photo_url && file_exists(public_path($roomType->photo_url))) {
                    unlink(public_path($roomType->photo_url));
                }
                $data['photo_url'] = null;
            }
            
            // Handle new photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($roomType->photo_url && file_exists(public_path($roomType->photo_url))) {
                    unlink(public_path($roomType->photo_url));
                }
                
                $photo = $request->file('photo');
                $filename = time() . '_' . Str::slug($request->name) . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('room-types', $filename, 'public');
                $data['photo_url'] = '/storage/' . $path;
            }
            
            $roomType->update($data);
            
            Log::info('Room type updated successfully', [
                'id' => $roomType->id, 
                'name' => $roomType->name,
                'price' => $roomType->price,
                'capacity' => $roomType->capacity
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Room type "' . $roomType->name . '" updated successfully!',
                    'room_type' => $roomType
                ]);
            }
            
            return redirect()->route('admin.room-types.index')
                ->with('success', 'Room type "' . $roomType->name . '" updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Room type update failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update room type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to update room type: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete room type.
     */
    public function destroy($id)
    {
        try {
            $roomType = RoomType::findOrFail($id);
            $name = $roomType->name;
            $roomsCount = $roomType->rooms()->count();
            
            // Check if room type has rooms
            if ($roomsCount > 0) {
                $message = 'Cannot delete room type "' . $name . '" because it has ' . $roomsCount . ' rooms assigned!';
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                
                return redirect()->route('admin.room-types.index')
                    ->with('error', $message);
            }
            
            // Delete photo if exists
            if ($roomType->photo_url && file_exists(public_path($roomType->photo_url))) {
                unlink(public_path($roomType->photo_url));
            }
            
            $roomType->delete();
            
            Log::info('Room type deleted successfully', ['id' => $id, 'name' => $name]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Room type "' . $name . '" deleted successfully!'
                ]);
            }
            
            return redirect()->route('admin.room-types.index')
                ->with('success', 'Room type "' . $name . '" deleted successfully!');
                
        } catch (\Exception $e) {
            Log::error('Room type deletion failed: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete room type: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.room-types.index')
                ->with('error', 'Failed to delete room type: ' . $e->getMessage());
        }
    }
    
    /**
     * Get room type details for AJAX.
     */
    public function getRoomTypeDetails($id)
    {
        try {
            $roomType = RoomType::withCount('rooms')->findOrFail($id);
            
            $availableRooms = $roomType->rooms()->where('status', 'available')->count();
            $occupiedRooms = $roomType->rooms()->where('status', 'occupied')->count();
            $maintenanceRooms = $roomType->rooms()->where('status', 'maintenance')->count();
            
            return response()->json([
                'success' => true,
                'room_type' => [
                    'id' => $roomType->id,
                    'name' => $roomType->name,
                    'price' => $roomType->price,
                    'price_formatted' => 'Rp ' . number_format($roomType->price, 0, ',', '.'),
                    'capacity' => $roomType->capacity,
                    'description' => $roomType->description,
                    'photo_url' => $roomType->photo_url,
                    'total_rooms' => $roomType->rooms_count,
                    'available_rooms' => $availableRooms,
                    'occupied_rooms' => $occupiedRooms,
                    'maintenance_rooms' => $maintenanceRooms,
                    'created_at' => $roomType->created_at ? $roomType->created_at->format('d M Y') : '-',
                    'updated_at' => $roomType->updated_at ? $roomType->updated_at->format('d M Y') : '-'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get room type details failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Room type not found'
            ], 404);
        }
    }
    
    /**
     * Search room types.
     */
    public function search(Request $request)
    {
        try {
            $query = RoomType::withCount('rooms');
            
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            }
            
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }
            
            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }
            
            if ($request->has('capacity') && $request->capacity) {
                $query->where('capacity', '>=', $request->capacity);
            }
            
            $roomTypes = $query->orderBy('created_at', 'desc')->get();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'count' => $roomTypes->count(),
                    'room_types' => $roomTypes
                ]);
            }
            
            $stats = [
                'total' => $roomTypes->count(),
                'total_rooms' => $roomTypes->sum('rooms_count'),
                'avg_price' => $roomTypes->avg('price'),
            ];
            
            return view('admin.room-types.index', compact('roomTypes', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Search failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.room-types.index')
                ->with('error', 'Search failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Export room types data.
     */
    public function export()
    {
        try {
            $roomTypes = RoomType::withCount('rooms')->get();
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="room_types_export_' . date('Y-m-d') . '.csv"',
            ];
            
            $callback = function() use ($roomTypes) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for special characters
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Add headers
                fputcsv($file, ['ID', 'Name', 'Price', 'Capacity', 'Total Rooms', 'Description', 'Created At']);
                
                // Add data
                foreach ($roomTypes as $type) {
                    fputcsv($file, [
                        $type->id,
                        $type->name,
                        number_format($type->price, 0, ',', '.'),
                        $type->capacity . ' guests',
                        $type->rooms_count,
                        $type->description ?? '-',
                        $type->created_at ? $type->created_at->format('Y-m-d H:i:s') : ''
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return redirect()->route('admin.room-types.index')
                ->with('error', 'Failed to export: ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk delete room types.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:room_types,id'
            ]);
            
            $deletedCount = 0;
            $failedCount = 0;
            
            foreach ($request->ids as $id) {
                $roomType = RoomType::find($id);
                if ($roomType && $roomType->rooms()->count() == 0) {
                    // Delete photo if exists
                    if ($roomType->photo_url && file_exists(public_path($roomType->photo_url))) {
                        unlink(public_path($roomType->photo_url));
                    }
                    $roomType->delete();
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            }
            
            $message = $deletedCount . ' room types deleted successfully.';
            if ($failedCount > 0) {
                $message .= ' ' . $failedCount . ' room types could not be deleted (has rooms assigned).';
            }
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted' => $deletedCount,
                    'failed' => $failedCount
                ]);
            }
            
            return redirect()->route('admin.room-types.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            Log::error('Bulk delete failed: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete room types: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.room-types.index')
                ->with('error', 'Failed to delete room types: ' . $e->getMessage());
        }
    }
    
    /**
     * Get room type statistics for dashboard.
     */
    public function getStatistics()
    {
        try {
            $roomTypes = RoomType::withCount('rooms')->get();
            
            $stats = [
                'total_types' => $roomTypes->count(),
                'total_rooms' => $roomTypes->sum('rooms_count'),
                'avg_price' => $roomTypes->avg('price'),
                'max_price' => $roomTypes->max('price'),
                'min_price' => $roomTypes->min('price'),
                'most_expensive' => $roomTypes->sortByDesc('price')->first(),
                'most_rooms' => $roomTypes->sortByDesc('rooms_count')->first(),
                'by_capacity' => []
            ];
            
            // Group by capacity
            for ($i = 1; $i <= 10; $i++) {
                $stats['by_capacity'][$i] = [
                    'capacity' => $i,
                    'count' => $roomTypes->where('capacity', $i)->count(),
                    'rooms' => $roomTypes->where('capacity', $i)->sum('rooms_count')
                ];
            }
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get statistics failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}