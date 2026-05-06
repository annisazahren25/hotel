@extends('admin.layouts.app')

@section('page_title', 'Guest Management')
@section('page_subtitle', 'Manage all hotel guests and customers')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Guest Management</h1>
            <p class="text-gray-400 text-sm mt-1">Manage all registered guests and customers</p>
        </div>
        <a href="{{ route('admin.guests.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i class="fas fa-download"></i>
            Export Data
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Guests</p>
                    <p class="text-2xl font-bold text-white">{{ $guests->total() }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-yellow-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Active Bookings</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $guests->sum(function($g) { return $g->bookings->whereIn('status', ['confirmed', 'checked_in'])->count(); }) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Orders</p>
                    <p class="text-2xl font-bold text-purple-400">{{ $guests->sum(function($g) { return $g->restaurantOrders->count(); }) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-purple-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">New This Month</p>
                    <p class="text-2xl font-bold text-green-400">{{ $guests->filter(function($g) { return $g->created_at->month == now()->month; })->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-plus text-green-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.guests.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by name, email, or phone..." 
                       value="{{ request('search') }}"
                       class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none">
            </div>
            <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-search mr-2"></i> Search
            </button>
            @if(request('search'))
            <a href="{{ route('admin.guests.index') }}" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Guests Table -->
    <div class="card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Bookings</th>
                        <th>Orders</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                    <tr>
                        <td class="text-gray-400">#{{ $guest->id }}</td>
                        <td class="text-white font-semibold">{{ $guest->name }}</td>
                        <td class="text-gray-300">{{ $guest->email }}</td>
                        <td class="text-gray-300">{{ $guest->phone ?? '-' }}</td>
                        <td>
                            <span class="px-2 py-1 rounded-full text-xs 
                                @if($guest->bookings->whereIn('status', ['confirmed', 'checked_in'])->count() > 0)
                                    bg-blue-500/20 text-blue-400
                                @else
                                    bg-gray-500/20 text-gray-400
                                @endif">
                                {{ $guest->bookings->count() }} bookings
                            </span>
                        </td>
                        <td>
                            <span class="px-2 py-1 rounded-full text-xs 
                                @if($guest->restaurantOrders->count() > 0)
                                    bg-purple-500/20 text-purple-400
                                @else
                                    bg-gray-500/20 text-gray-400
                                @endif">
                                {{ $guest->restaurantOrders->count() }} orders
                            </span>
                        </td>
                        <td class="text-gray-300">{{ $guest->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.guests.show', $guest->id) }}" 
                                   class="text-blue-500 hover:text-blue-400 transition" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($guest->role == 'customer')
                                <button onclick="upgradeGuest({{ $guest->id }}, '{{ $guest->name }}')" 
                                        class="text-yellow-500 hover:text-yellow-400 transition" title="Upgrade to Staff">
                                    <i class="fas fa-user-tie"></i>
                                </button>
                                @endif
                                <button onclick="deleteGuest({{ $guest->id }}, '{{ $guest->name }}')" 
                                        class="text-red-500 hover:text-red-400 transition" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-400">
                            <i class="fas fa-users text-4xl mb-2"></i>
                            <p>No guests found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $guests->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<script>
function upgradeGuest(id, name) {
    Swal.fire({
        title: 'Upgrade Guest?',
        html: `Are you sure you want to upgrade <strong class="text-yellow-500">${name}</strong> to staff?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#D4AF37',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Upgrade!',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/guests/${id}/upgrade`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', role: 'staff' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Upgraded!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to upgrade guest',
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            });
        }
    });
}

function deleteGuest(id, name) {
    Swal.fire({
        title: 'Delete Guest?',
        html: `Are you sure you want to delete <strong class="text-yellow-500">${name}</strong>?<br><br>This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/guests/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to delete guest',
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            });
        }
    });
}

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#D4AF37',
        background: '#1f2937',
        color: '#fff',
        timer: 3000
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: "{{ session('error') }}",
        confirmButtonColor: '#D4AF37',
        background: '#1f2937',
        color: '#fff'
    });
@endif
</script>
@endsection