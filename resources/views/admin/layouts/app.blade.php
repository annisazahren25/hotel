<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - Hotel Val Royale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Sweet Alert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        * { 
            font-family: 'Inter', sans-serif; 
        }
        
        body { 
            background: #0a0a0a; 
        }
        
        /* Sidebar Styles */
        .sidebar { 
            background: linear-gradient(180deg, #0f0f1a 0%, #0a0a0f 100%);
            border-right: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .sidebar-link { 
            transition: all 0.3s ease; 
            border-left: 3px solid transparent;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(212, 175, 55, 0.1);
            color: #D4AF37;
            border-left-color: #D4AF37;
        }
        
        /* Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1a1a2e;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #D4AF37;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #e5c83e;
        }
        
        /* Card Styles */
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-available { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-occupied { background: rgba(59, 130, 246, 0.2); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
        .status-maintenance { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        .status-pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .status-confirmed { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-checked_in { background: rgba(59, 130, 246, 0.2); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
        .status-checked_out { background: rgba(107, 114, 128, 0.2); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.3); }
        .status-cancelled { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        .status-cancellation_requested { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .status-paid { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-ordered { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        
        /* Button Styles */
        .btn-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            transition: all 0.3s ease;
        }
        
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }
        
        /* Table Styles */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th {
            text-align: left;
            padding: 16px;
            background: rgba(255, 255, 255, 0.05);
            color: #9ca3af;
            font-weight: 600;
            font-size: 0.875rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-table td {
            padding: 16px;
            color: #e5e7eb;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .admin-table tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        
        /* Form Styles */
        .form-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 8px;
            padding: 10px 16px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }
        
        .form-label {
            display: block;
            color: #9ca3af;
            margin-bottom: 8px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(0,0,0,0.3);
            border-radius: 50%;
            border-top-color: #D4AF37;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Fade In Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-72 fixed h-full overflow-y-auto z-10">
            <!-- Logo -->
            <div class="p-6 border-b border-yellow-500/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-crown text-black text-xl"></i>
                    </div>
                    <div>
                        <span class="font-bold text-xl text-yellow-500">Val Royale</span>
                        <p class="text-gray-500 text-xs">Hotel Management</p>
                    </div>
                </div>
            </div>
            
            <!-- USER INFO & LOGOUT -->
            <div class="p-4 border-b border-yellow-500/20 bg-gradient-to-b from-white/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-user text-black text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-gray-400 text-xs">
                            <i class="fas fa-shield-alt mr-1 text-yellow-500"></i>
                            {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
                        </p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" id="logoutForm" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full sidebar-link flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-gray-400 hover:bg-red-500/10 hover:text-red-400 transition text-sm">
                        <i class="fas fa-sign-out-alt w-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
            <!-- END USER INFO -->
            
            <nav class="p-4">
                <div class="mb-6">
                    <p class="text-gray-600 text-xs uppercase tracking-wider px-4 mb-2">Main Menu</p>
                    
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <!-- Room Types -->
                    <a href="{{ route('admin.room-types.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.room-types.*') ? 'active' : '' }}">
                        <i class="fas fa-building w-5"></i>
                        <span>Room Types</span>
                    </a>
                    
                    <!-- Rooms -->
                    <a href="{{ route('admin.rooms.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                        <i class="fas fa-bed w-5"></i>
                        <span>Rooms</span>
                    </a>
                    
                    <!-- Bookings -->
                    <a href="{{ route('admin.bookings.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check w-5"></i>
                        <span>Bookings</span>
                    </a>
                    
                    <!-- ========== CANCELLATION REQUESTS (NEW) ========== -->
                    <a href="{{ route('admin.cancellations.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.cancellations.*') ? 'active' : '' }}">
                        <i class="fas fa-clock w-5 text-orange-500"></i>
                        <span>Cancellation Requests</span>
                        @php
                            $pendingCount = \App\Models\Booking::where('status', 'cancellation_requested')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                    <!-- ========== END CANCELLATION REQUESTS ========== -->
                    
                    <!-- ========== RESTAURANT DROPDOWN ========== -->
                    <div class="mt-2">
                        <button onclick="toggleRestaurantMenu()" class="w-full sidebar-link flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-utensils w-5"></i>
                                <span>Restaurant</span>
                            </div>
                            <i id="restaurantArrow" class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                        </button>
                        
                        <!-- Submenu -->
                        <div id="restaurantSubmenu" class="ml-8 mt-1 space-y-1 hidden">
                            <a href="{{ route('admin.restaurant.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg text-gray-400 text-sm hover:text-yellow-500 {{ request()->routeIs('admin.restaurant.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-chart-pie w-4"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.restaurant.orders') }}" class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg text-gray-400 text-sm hover:text-yellow-500 {{ request()->routeIs('admin.restaurant.orders') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list w-4"></i>
                                <span>Orders</span>
                            </a>
                            <a href="{{ route('admin.restaurant.menu') }}" class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg text-gray-400 text-sm hover:text-yellow-500 {{ request()->routeIs('admin.restaurant.menu') ? 'active' : '' }}">
                                <i class="fas fa-utensils w-4"></i>
                                <span>Menu Management</span>
                            </a>
                        </div>
                    </div>
                    <!-- ========== END RESTAURANT DROPDOWN ========== -->
                    
                    <!-- Guests -->
                    <a href="{{ route('admin.guests.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.guests.*') ? 'active' : '' }}">
                        <i class="fas fa-users w-5"></i>
                        <span>Guests</span>
                    </a>
                    
                    <!-- Payments -->
                    <a href="{{ route('admin.payments.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>Payments</span>
                    </a>
                    
                    @if(Auth::user()->isSuperAdmin())
                    <div class="mt-4">
                        <p class="text-gray-600 text-xs uppercase tracking-wider px-4 mb-2">System</p>
                        
                        <a href="{{ route('admin.staff.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie w-5"></i>
                            <span>Staff Management</span>
                        </a>
                        
                        <a href="{{ route('admin.reports.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar w-5"></i>
                            <span>Reports</span>
                        </a>
                        
                        <a href="{{ route('admin.settings') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 mb-1">
                            <i class="fas fa-cog w-5"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                    @endif
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="ml-72 flex-1 overflow-y-auto">
            <!-- Top Bar -->
            <div class="sticky top-0 z-20 bg-black/50 backdrop-blur-md border-b border-yellow-500/20 px-8 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-white">
                            @yield('page_title', 'Dashboard')
                        </h1>
                        <p class="text-gray-400 text-sm mt-1">
                            @yield('page_subtitle', 'Welcome back, ' . Auth::user()->name)
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Current Time -->
                        <div class="text-right">
                            <p class="text-gray-400 text-xs" id="currentDate"></p>
                            <p class="text-white font-semibold" id="currentTime"></p>
                        </div>
                        <!-- Refresh Button -->
                        <button onclick="location.reload()" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 transition">
                            <i class="fas fa-sync-alt text-gray-400"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="p-8">
                @if(session('success'))
                    <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span class="text-green-400">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <span class="text-red-400">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Toggle Restaurant Dropdown
        function toggleRestaurantMenu() {
            const submenu = document.getElementById('restaurantSubmenu');
            const arrow = document.getElementById('restaurantArrow');
            
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                submenu.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }
        
        // Keep dropdown open when on restaurant pages
        document.addEventListener('DOMContentLoaded', function() {
            const restaurantRoutes = [
                'admin.restaurant.dashboard', 
                'admin.restaurant.orders', 
                'admin.restaurant.menu',
                'admin.restaurant.order.detail',
                'admin.restaurant.menu.edit',
                'admin.restaurant.menu.create'
            ];
            const cancellationRoutes = [
                'admin.cancellations.index',
                'admin.cancellations.show'
            ];
            const currentRoute = '{{ request()->route()->getName() }}';
            
            if (restaurantRoutes.includes(currentRoute)) {
                const submenu = document.getElementById('restaurantSubmenu');
                const arrow = document.getElementById('restaurantArrow');
                if (submenu) {
                    submenu.classList.remove('hidden');
                    arrow.style.transform = 'rotate(180deg)';
                }
            }
        });
        
        // Global Sweet Alert Functions
        window.showToast = function(title, icon = 'success', timer = 3000) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: timer,
                timerProgressBar: true,
                icon: icon,
                title: title,
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
        
        window.showConfirm = function(title, text, confirmButtonText = 'Yes, proceed!', icon = 'warning') {
            return Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#D4AF37',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
                background: '#1f2937',
                color: '#fff'
            });
        };
        
        window.showDeleteConfirm = function(title, text) {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                background: '#1f2937',
                color: '#fff'
            });
        };
        
        // Logout confirmation
        const logoutForm = document.getElementById('logoutForm');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                showConfirm('Logout Confirmation', 'Are you sure you want to logout?', 'Yes, Logout', 'question')
                    .then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
            });
        }
        
        // Update time
        function updateTime() {
            const now = new Date();
            const dateEl = document.getElementById('currentDate');
            const timeEl = document.getElementById('currentTime');
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
            if (timeEl) {
                timeEl.textContent = now.toLocaleTimeString('id-ID');
            }
        }
        updateTime();
        setInterval(updateTime, 1000);
        
        // Global AJAX error handling
        $(document).ajaxError(function(event, xhr, settings, error) {
            if (xhr.status === 419) {
                showAlert('Session Expired', 'Your session has expired. Please refresh the page.', 'error');
            } else if (xhr.status === 403) {
                showAlert('Access Denied', 'You do not have permission.', 'error');
            } else if (xhr.status === 500) {
                showAlert('Server Error', 'An internal server error occurred.', 'error');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                showAlert('Error', xhr.responseJSON.message, 'error');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>