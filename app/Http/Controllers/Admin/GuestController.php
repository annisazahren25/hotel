<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::where('role', 'customer');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        $guests = $query->latest()->paginate(20);
        
        return view('admin.guests.index', compact('guests'));
    }
    
    public function show($id)
    {
        $guest = Guest::with(['bookings.room', 'restaurantOrders.items.menu'])->findOrFail($id);
        return view('admin.guests.show', compact('guest'));
    }
    
    public function upgrade(Request $request, $id)
    {
        try {
            $guest = Guest::findOrFail($id);
            $guest->role = $request->role ?? 'staff';
            $guest->save();
            
            return response()->json([
                'success' => true,
                'message' => "{$guest->name} has been upgraded to " . ucfirst($guest->role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upgrade guest'
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $guest = Guest::findOrFail($id);
            $name = $guest->name;
            
            // Check if guest has active bookings
            if ($guest->bookings()->whereIn('status', ['pending', 'confirmed', 'checked_in'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete guest with active bookings!'
                ], 400);
            }
            
            $guest->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Guest {$name} deleted successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete guest'
            ], 500);
        }
    }
    
    public function export()
    {
        // TODO: Implement export
        return redirect()->back()->with('info', 'Export feature coming soon');
    }
}