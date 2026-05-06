@extends('admin.layouts.app')

@section('page_title', 'Restaurant Menu')
@section('page_subtitle', 'Manage all food and beverage items')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Restaurant Menu</h1>
            <p class="text-gray-400 text-sm mt-1">Manage all food and beverage items</p>
        </div>
        <button onclick="toggleModal()" class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Add Menu Item
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Items</p>
                    <p class="text-2xl font-bold text-white">{{ $menus->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-yellow-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Categories</p>
                    <p class="text-2xl font-bold text-white">{{ $menus->unique('category')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-blue-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Avg Price</p>
                    <p class="text-2xl font-bold text-white">Rp {{ number_format($menus->avg('price') ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Available</p>
                    <p class="text-2xl font-bold text-green-400">{{ $menus->where('is_available', true)->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-teal-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-teal-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <button class="category-filter px-4 py-2 rounded-lg text-sm transition all-categories active bg-yellow-500/20 text-yellow-500" data-category="all">
            All Items
        </button>
        @foreach($menus->unique('category') as $cat)
        <button class="category-filter px-4 py-2 rounded-lg text-sm transition" data-category="{{ $cat->category }}">
            {{ $cat->category }}
        </button>
        @endforeach
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="menuGrid">
        @foreach($menus as $menu)
        <div class="card rounded-xl overflow-hidden hover:transform hover:scale-105 transition-all duration-300" data-category="{{ $menu->category }}">
            @if($menu->photo_url)
            <img src="{{ $menu->photo_url }}" alt="{{ $menu->name }}" class="w-full h-48 object-cover">
            @else
            <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                <i class="fas fa-utensils text-4xl text-gray-500"></i>
            </div>
            @endif
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-bold text-white">{{ $menu->name }}</h3>
                    <span class="text-yellow-500 font-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-400 text-sm px-2 py-1 rounded-full bg-white/5">{{ $menu->category }}</span>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="toggle-availability toggle-switch" data-id="{{ $menu->id }}" {{ $menu->is_available ? 'checked' : '' }}>
                        <span class="ml-2 text-xs {{ $menu->is_available ? 'text-green-400' : 'text-red-400' }}">
                            {{ $menu->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </label>
                </div>
                <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $menu->description ?? 'No description' }}</p>
                <div class="flex gap-2">
                    <button onclick="editMenu({{ $menu }})" class="flex-1 bg-yellow-500 text-black px-3 py-2 rounded-lg text-sm hover:bg-yellow-600 transition">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <button onclick="deleteMenu({{ $menu->id }}, '{{ $menu->name }}')" class="flex-1 bg-red-500/20 text-red-500 px-3 py-2 rounded-lg text-sm hover:bg-red-500/30 transition">
                        <i class="fas fa-trash-alt mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($menus->isEmpty())
    <div class="text-center py-12">
        <i class="fas fa-utensils text-6xl text-gray-600 mb-4"></i>
        <p class="text-gray-400">No menu items found</p>
        <button onclick="toggleModal()" class="mt-4 text-yellow-500 hover:text-yellow-400">
            + Add your first menu item
        </button>
    </div>
    @endif
</div>

<!-- Modal Add/Edit Menu - FIXED SCROLLABLE -->
<div id="menuModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gray-900 px-6 pt-6 pb-4 border-b border-gray-700 z-10">
            <div class="flex justify-between items-center">
                <h2 id="modalTitle" class="text-xl font-bold text-white">Add Menu Item</h2>
                <button onclick="toggleModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="px-6 py-4">
            <form id="menuForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Name *</label>
                    <input type="text" name="name" id="menuName" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Price (Rp) *</label>
                    <input type="number" name="price" id="menuPrice" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Category *</label>
                    <select name="category" id="menuCategory" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" required style="color: white; background-color: #1f2937;">
                        <option value="Food" style="background-color: #1f2937; color: white;">🍔 Food</option>
                        <option value="Beverage" style="background-color: #1f2937; color: white;">🥤 Beverage</option>
                        <option value="Dessert" style="background-color: #1f2937; color: white;">🍰 Dessert</option>
                        <option value="Appetizer" style="background-color: #1f2937; color: white;">🥗 Appetizer</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="menuDescription" rows="4" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Photo (Max 10MB)</label>
                    <input type="file" name="photo" accept="image/*" id="menuPhoto" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-yellow-500 file:text-black hover:file:bg-yellow-600 cursor-pointer">
                    <div id="currentPhoto" class="mt-2 hidden">
                        <p class="text-gray-400 text-sm mb-1">Current Photo:</p>
                        <img id="currentPhotoImg" src="" class="h-20 rounded-lg object-cover">
                    </div>
                    <p class="text-gray-500 text-xs mt-1">Supported: JPG, PNG, GIF, WEBP</p>
                </div>
                
                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_available" id="menuAvailable" value="1" checked class="mr-2 w-4 h-4">
                        <span class="text-gray-300 text-sm">Available for ordering</span>
                    </label>
                </div>
                
                <div class="flex gap-3 sticky bottom-0 bg-gray-900 py-4 border-t border-gray-700 -mx-6 px-6">
                    <button type="submit" id="submitBtn" class="flex-1 bg-yellow-500 text-black px-4 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition">
                        <i class="fas fa-save mr-2"></i> Save
                    </button>
                    <button type="button" onclick="toggleModal()" class="flex-1 bg-gray-700 text-gray-300 px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .toggle-switch {
        width: 40px;
        height: 20px;
        background-color: #ef4444;
        border-radius: 20px;
        appearance: none;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }
    .toggle-switch:checked {
        background-color: #10b981;
    }
    .toggle-switch::before {
        content: '';
        width: 16px;
        height: 16px;
        background-color: white;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: all 0.3s ease;
    }
    .toggle-switch:checked::before {
        transform: translateX(20px);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Custom scrollbar for modal */
    .max-h-\[90vh\]::-webkit-scrollbar {
        width: 6px;
    }
    .max-h-\[90vh\]::-webkit-scrollbar-track {
        background: #1f2937;
        border-radius: 10px;
    }
    .max-h-\[90vh\]::-webkit-scrollbar-thumb {
        background: #D4AF37;
        border-radius: 10px;
    }
    
    /* FIX: Make select options visible */
    select option {
        background-color: #1f2937 !important;
        color: #ffffff !important;
        padding: 8px !important;
    }
    
    select option:hover {
        background-color: #374151 !important;
    }
    
    select option:checked {
        background-color: #D4AF37 !important;
        color: #000000 !important;
    }
    
    /* Style untuk select itu sendiri */
    select {
        background-color: #1f2937 !important;
        color: #ffffff !important;
    }
    
    /* Fix untuk category filter button active */
    .category-filter.active {
        background-color: rgba(212, 175, 55, 0.2);
        color: #D4AF37;
    }
</style>

<script>
// Toggle Modal
function toggleModal() {
    const modal = document.getElementById('menuModal');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('modalTitle').innerText = 'Add Menu Item';
        document.getElementById('menuForm').action = '{{ route("admin.restaurant.menu.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('menuName').value = '';
        document.getElementById('menuPrice').value = '';
        document.getElementById('menuCategory').value = 'Food';
        document.getElementById('menuDescription').value = '';
        document.getElementById('menuAvailable').checked = true;
        document.getElementById('menuPhoto').value = '';
        document.getElementById('currentPhoto').classList.add('hidden');
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Edit Menu
function editMenu(menu) {
    toggleModal();
    document.getElementById('modalTitle').innerText = 'Edit Menu Item';
    document.getElementById('menuForm').action = '/admin/restaurant/menu/' + menu.id;
    document.getElementById('methodField').value = 'PUT';
    document.getElementById('menuName').value = menu.name;
    document.getElementById('menuPrice').value = menu.price;
    document.getElementById('menuCategory').value = menu.category;
    document.getElementById('menuDescription').value = menu.description || '';
    document.getElementById('menuAvailable').checked = menu.is_available == 1;
    if (menu.photo_url) {
        document.getElementById('currentPhotoImg').src = menu.photo_url;
        document.getElementById('currentPhoto').classList.remove('hidden');
    } else {
        document.getElementById('currentPhoto').classList.add('hidden');
    }
}

// Delete Menu with Sweet Alert
function deleteMenu(id, name) {
    Swal.fire({
        title: 'Delete Menu Item?',
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
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#1f2937',
                color: '#fff'
            });
            
            $.ajax({
                url: `/admin/restaurant/menu/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to delete menu item',
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            });
        }
    });
}

// Toggle Availability with Sweet Alert
$(document).on('change', '.toggle-availability', function() {
    const id = $(this).data('id');
    const isChecked = $(this).is(':checked');
    const status = isChecked ? 'available' : 'unavailable';
    const toggle = $(this);
    
    Swal.fire({
        title: 'Update Status?',
        text: `Make this item ${status} for ordering?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#D4AF37',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, update!',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff'
    }).then((result) => {
        if (!result.isConfirmed) {
            toggle.prop('checked', !isChecked);
            return;
        }
        
        Swal.fire({
            title: 'Updating...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            background: '#1f2937',
            color: '#fff'
        });
        
        $.ajax({
            url: `/admin/restaurant/menu/${id}/toggle`,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message,
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff',
                        timer: 2000
                    });
                    const statusText = response.is_available ? 'Available' : 'Unavailable';
                    const statusColor = response.is_available ? 'text-green-400' : 'text-red-400';
                    toggle.siblings('span').text(statusText).removeClass('text-green-400 text-red-400').addClass(statusColor);
                } else {
                    toggle.prop('checked', !isChecked);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            },
            error: function() {
                Swal.close();
                toggle.prop('checked', !isChecked);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update status',
                    confirmButtonColor: '#D4AF37',
                    background: '#1f2937',
                    color: '#fff'
                });
            }
        });
    });
});

// Category Filter
$('.category-filter').on('click', function() {
    $('.category-filter').removeClass('active bg-yellow-500/20 text-yellow-500');
    $(this).addClass('active bg-yellow-500/20 text-yellow-500');
    
    const category = $(this).data('category');
    
    if (category === 'all') {
        $('#menuGrid > div').show();
    } else {
        $('#menuGrid > div').hide();
        $(`#menuGrid > div[data-category="${category}"]`).show();
    }
});

// Form Submit with Loading
$('#menuForm').on('submit', function(e) {
    const btn = $('#submitBtn');
    btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');
    btn.prop('disabled', true);
});

// Flash messages from session with Sweet Alert
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

// Global helper functions for Sweet Alert
window.showToast = function(message, icon = 'success') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: icon,
        title: message,
        background: '#1f2937',
        color: '#fff'
    });
};

window.showAlert = function(title, message, icon = 'success') {
    Swal.fire({
        title: title,
        text: message,
        icon: icon,
        confirmButtonColor: '#D4AF37',
        background: '#1f2937',
        color: '#fff'
    });
};

window.showLoading = function(title = 'Processing...') {
    Swal.fire({
        title: title,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
        background: '#1f2937',
        color: '#fff'
    });
};

window.closeLoading = function() {
    Swal.close();
};
</script>
@endsection