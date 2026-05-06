<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hotel Val Royale - @yield('title', 'Luxury Stay')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .font-serif {
            font-family: 'Playfair Display', serif;
        }
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gold-text {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .btn-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(212, 175, 55, 0.3);
        }
        .room-card {
            transition: all 0.3s ease;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .status-available {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        .status-occupied {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        .status-maintenance {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        .custom-scroll::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #1a1a2e;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #D4AF37;
            border-radius: 4px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .group:hover .group-hover\:block {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }
        
        .hover\:glass-card-hover:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="text-gray-200">
    <!-- Navbar -->
    <nav class="glass-card fixed top-0 left-0 right-0 z-50 border-b border-gold-500/20">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center space-x-2">
                    <i class="fas fa-crown text-2xl gold-text"></i>
                    <span class="font-serif text-2xl font-bold gold-text">Val Royale</span>
                </a>
                
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="hover:gold-text transition">Home</a>
                    <a href="/rooms" class="hover:gold-text transition">Rooms</a>
                    <a href="/restaurant" class="hover:gold-text transition">Restaurant</a>
                    @auth
                        <a href="{{ route('my.bookings') }}" class="hover:gold-text transition">My Bookings</a>
                        <a href="{{ route('restaurant.orders') }}" class="hover:gold-text transition">My Orders</a>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hover:gold-text transition">Admin Panel</a>
                        @endif
                    @endauth
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative group">
                            <button class="flex items-center space-x-2 glass-card px-4 py-2 rounded-full hover:bg-white/10 transition">
                                <i class="fas fa-user-circle text-xl gold-text"></i>
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-56 glass-card rounded-lg overflow-hidden hidden group-hover:block shadow-xl">
                                <div class="py-2">
                                    <div class="px-4 py-2 border-b border-gold-500/20">
                                        <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
                                        <p class="text-xs gold-text mt-1">{{ ucfirst(Auth::user()->role) }}</p>
                                    </div>
                                    <a href="{{ route('my.bookings') }}" class="flex items-center px-4 py-2 hover:bg-white/10 transition">
                                        <i class="fas fa-calendar-alt w-5 gold-text"></i>
                                        <span class="ml-3">My Bookings</span>
                                    </a>
                                    <a href="{{ route('restaurant.orders') }}" class="flex items-center px-4 py-2 hover:bg-white/10 transition">
                                        <i class="fas fa-utensils w-5 gold-text"></i>
                                        <span class="ml-3">My Orders</span>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-white/10 transition">
                                            <i class="fas fa-chart-line w-5 gold-text"></i>
                                            <span class="ml-3">Admin Panel</span>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gold-500/20 mt-2 pt-2">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 hover:bg-red-500/20 transition text-red-400">
                                            <i class="fas fa-sign-out-alt w-5"></i>
                                            <span class="ml-3">Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-full glass-card hover:bg-white/10 transition">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-full btn-gold text-black font-semibold hover:shadow-lg transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-24 pb-12">
        <div class="container mx-auto px-6">
            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 mb-6 animate-fade-in">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6 animate-fade-in">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                        <div>
                            <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <footer class="glass-card border-t border-gold-500/20 py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; 2024 Hotel Val Royale - Where Luxury Meets Elegance</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
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
        
        // Logout confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showConfirm('Logout Confirmation', 'Are you sure you want to logout?', 'Yes, Logout', 'question')
                        .then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>