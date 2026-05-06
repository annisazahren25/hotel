@extends('layouts.app')

@section('title', 'Luxury Hotel & Resort')

@section('content')
<!-- Hero Section -->
<section class="relative h-screen -mt-24 mb-12 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-black/60 z-10"></div>
        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1920" alt="Hero" class="w-full h-full object-cover">
    </div>
    
    <!-- Hero Content -->
    <div class="relative z-20 h-full flex items-center justify-center text-center">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="inline-block px-6 py-2 glass-card rounded-full mb-6 animate-fade-in">
                    <span class="text-yellow-500 text-sm font-semibold tracking-wider">WELCOME TO VAL ROYALE</span>
                </div>
                <h1 class="font-serif text-5xl md:text-7xl lg:text-8xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-6 animate-fade-in">
                    Where Dreams<br>Come to Life
                </h1>
                <p class="text-lg md:text-2xl text-gray-200 mb-8 animate-fade-in max-w-2xl mx-auto">
                    Experience unparalleled luxury and exceptional service at the finest hotel in the city
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in">
                    <a href="/rooms" class="px-8 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold rounded-full text-lg hover:scale-105 transition transform inline-flex items-center justify-center gap-2">
                        <i class="fas fa-calendar-check"></i> Book Your Stay
                    </a>
                    <a href="#explore" class="px-8 py-4 glass-card text-white font-semibold rounded-full text-lg hover:bg-white/10 transition transform inline-flex items-center justify-center gap-2">
                        <i class="fas fa-play"></i> Explore More
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Down -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
        <a href="#booking" class="text-white text-2xl">
            <i class="fas fa-chevron-down"></i>
        </a>
    </div>
</section>

<!-- Booking Form Section -->
<section id="booking" class="py-16 -mt-20 relative z-30">
    <div class="container mx-auto px-6">
        <div class="glass-card rounded-2xl p-8 max-w-5xl mx-auto shadow-2xl">
            <form method="POST" action="{{ route('rooms.check') }}" class="grid md:grid-cols-4 gap-4" id="availabilityForm">
                @csrf
                <div>
                    <label class="block text-yellow-500 text-sm mb-2 font-semibold">
                        <i class="fas fa-calendar-alt text-yellow-500 mr-2"></i>Check In
                    </label>
                    <input type="date" name="check_in" id="check_in" class="date-input w-full bg-gray-800 border border-yellow-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 text-white" required>
                </div>
                <div>
                    <label class="block text-yellow-500 text-sm mb-2 font-semibold">
                        <i class="fas fa-calendar-alt text-yellow-500 mr-2"></i>Check Out
                    </label>
                    <input type="date" name="check_out" id="check_out" class="date-input w-full bg-gray-800 border border-yellow-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 text-white" required>
                </div>
                <div>
                    <label class="block text-yellow-500 text-sm mb-2 font-semibold">
                        <i class="fas fa-user text-yellow-500 mr-2"></i>Guests
                    </label>
                    <select name="guests" id="guests" class="w-full bg-gray-800 border border-yellow-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 text-white" required>
                        <option value="1" class="bg-gray-800 text-white">1 Guest</option>
                        <option value="2" selected class="bg-gray-800 text-white">2 Guests</option>
                        <option value="3" class="bg-gray-800 text-white">3 Guests</option>
                        <option value="4" class="bg-gray-800 text-white">4 Guests</option>
                        <option value="5" class="bg-gray-800 text-white">5 Guests</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-3 rounded-lg hover:shadow-lg transition">
                        Check Availability
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Rooms -->
<section class="py-20" id="explore">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <div class="inline-block px-6 py-2 glass-card rounded-full mb-4">
                <span class="text-yellow-500 text-sm font-semibold">LUXURY ACCOMMODATION</span>
            </div>
            <h2 class="font-serif text-4xl md:text-5xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-4">Our Signature Suites</h2>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto">
                Experience the epitome of luxury in our meticulously designed rooms and suites
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Room 1 -->
            <div class="glass-card rounded-2xl overflow-hidden group hover:transform hover:scale-105 transition-all duration-300">
                <div class="relative h-72 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800" alt="Deluxe Suite" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-3 py-1 rounded-full text-sm font-bold">
                        BEST SELLER
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                        <div class="flex items-center gap-2 text-yellow-500">
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <span class="text-white text-sm ml-2">(245 reviews)</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-2xl text-yellow-500 mb-2">Deluxe Suite</h3>
                    <p class="text-gray-300 mb-4">Luxurious suite with panoramic city views, king-size bed, and marble bathroom with rain shower.</p>
                    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm text-gray-400">
                        <span><i class="fas fa-bed text-yellow-500 mr-1"></i> 1 King Bed</span>
                        <span><i class="fas fa-bath text-yellow-500 mr-1"></i> Marble Bath</span>
                        <span><i class="fas fa-wifi text-yellow-500 mr-1"></i> Free WiFi</span>
                        <span><i class="fas fa-tv text-yellow-500 mr-1"></i> 65" TV</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-yellow-500/20">
                        <div>
                            <span class="text-2xl font-bold text-yellow-500">$299</span>
                            <span class="text-gray-400">/night</span>
                            <p class="text-xs text-gray-500">+taxes & fees</p>
                        </div>
                        <a href="/rooms" class="px-6 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black rounded-lg font-semibold hover:shadow-lg transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Room 2 -->
            <div class="glass-card rounded-2xl overflow-hidden group hover:transform hover:scale-105 transition-all duration-300">
                <div class="relative h-72 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800" alt="Executive Suite" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                        VIP ACCESS
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                        <div class="flex items-center gap-2 text-yellow-500">
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <span class="text-white text-sm ml-2">(189 reviews)</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-2xl text-yellow-500 mb-2">Executive Suite</h3>
                    <p class="text-gray-300 mb-4">Executive suite with separate living room, workspace, and exclusive lounge access with complimentary breakfast.</p>
                    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm text-gray-400">
                        <span><i class="fas fa-bed text-yellow-500 mr-1"></i> 1 King Bed</span>
                        <span><i class="fas fa-couch text-yellow-500 mr-1"></i> Living Area</span>
                        <span><i class="fas fa-coffee text-yellow-500 mr-1"></i> Minibar</span>
                        <span><i class="fas fa-laptop text-yellow-500 mr-1"></i> Workspace</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-yellow-500/20">
                        <div>
                            <span class="text-2xl font-bold text-yellow-500">$499</span>
                            <span class="text-gray-400">/night</span>
                            <p class="text-xs text-gray-500">+taxes & fees</p>
                        </div>
                        <a href="/rooms" class="px-6 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black rounded-lg font-semibold hover:shadow-lg transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Room 3 -->
            <div class="glass-card rounded-2xl overflow-hidden group hover:transform hover:scale-105 transition-all duration-300">
                <div class="relative h-72 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800" alt="Presidential Suite" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute top-4 right-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-3 py-1 rounded-full text-sm font-bold">
                        ULTIMATE LUXURY
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                        <div class="flex items-center gap-2 text-yellow-500">
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <span class="text-white text-sm ml-2">(97 reviews)</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-2xl text-yellow-500 mb-2">Presidential Suite</h3>
                    <p class="text-gray-300 mb-4">The pinnacle of luxury with private terrace, jacuzzi, butler service, and panoramic ocean views.</p>
                    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm text-gray-400">
                        <span><i class="fas fa-bed text-yellow-500 mr-1"></i> 2 King Beds</span>
                        <span><i class="fas fa-hot-tub text-yellow-500 mr-1"></i> Jacuzzi</span>
                        <span><i class="fas fa-concierge-bell text-yellow-500 mr-1"></i> Butler</span>
                        <span><i class="fas fa-champagne-glasses text-yellow-500 mr-1"></i> Champagne</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-yellow-500/20">
                        <div>
                            <span class="text-2xl font-bold text-yellow-500">$999</span>
                            <span class="text-gray-400">/night</span>
                            <p class="text-xs text-gray-500">+taxes & fees</p>
                        </div>
                        <a href="/rooms" class="px-6 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black rounded-lg font-semibold hover:shadow-lg transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="/rooms" class="inline-flex items-center gap-2 px-8 py-3 glass-card text-white rounded-full hover:bg-white/10 transition">
                View All Rooms <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Amenities Section -->
<section class="py-20 bg-gradient-to-r from-yellow-500/5 to-transparent">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <div class="inline-block px-6 py-2 glass-card rounded-full mb-4">
                <span class="text-yellow-500 text-sm font-semibold">WORLD-CLASS AMENITIES</span>
            </div>
            <h2 class="font-serif text-4xl md:text-5xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-4">Unmatched Experiences</h2>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto">
                Discover our exceptional facilities designed for your comfort and pleasure
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-card rounded-2xl p-6 text-center hover:transform hover:scale-105 transition-all duration-300 group">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-utensils text-4xl text-yellow-500"></i>
                </div>
                <h3 class="font-serif text-xl text-yellow-500 mb-2">Fine Dining</h3>
                <p class="text-gray-400 text-sm">3 Michelin-starred restaurants with international cuisine from world-renowned chefs</p>
            </div>
            
            <div class="glass-card rounded-2xl p-6 text-center hover:transform hover:scale-105 transition-all duration-300 group">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-spa text-4xl text-yellow-500"></i>
                </div>
                <h3 class="font-serif text-xl text-yellow-500 mb-2">Luxury Spa</h3>
                <p class="text-gray-400 text-sm">Rejuvenating treatments, traditional massages, and wellness programs</p>
            </div>
            
            <div class="glass-card rounded-2xl p-6 text-center hover:transform hover:scale-105 transition-all duration-300 group">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-dumbbell text-4xl text-yellow-500"></i>
                </div>
                <h3 class="font-serif text-xl text-yellow-500 mb-2">Fitness Center</h3>
                <p class="text-gray-400 text-sm">State-of-the-art equipment, personal trainers, and yoga classes</p>
            </div>
            
            <div class="glass-card rounded-2xl p-6 text-center hover:transform hover:scale-105 transition-all duration-300 group">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-yellow-500/20 to-yellow-600/20 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <i class="fas fa-swimmer text-4xl text-yellow-500"></i>
                </div>
                <h3 class="font-serif text-xl text-yellow-500 mb-2">Infinity Pool</h3>
                <p class="text-gray-400 text-sm">Heated outdoor pool with panoramic city views and poolside bar</p>
            </div>
        </div>
    </div>
</section>

<!-- Restaurant Preview -->
<section class="py-20">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block px-6 py-2 glass-card rounded-full mb-4">
                    <span class="text-yellow-500 text-sm font-semibold">CULINARY EXCELLENCE</span>
                </div>
                <h2 class="font-serif text-4xl md:text-5xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-4">Exquisite Dining Experience</h2>
                <p class="text-gray-300 mb-6 leading-relaxed">
                    Indulge in a gastronomic journey at our world-class restaurants. From authentic local cuisine to international delicacies, our expert chefs create unforgettable dining experiences.
                </p>
                <div class="space-y-4 mb-8">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-check-circle text-yellow-500 text-xl"></i>
                        <span class="text-gray-200">Asian & Western Cuisine Specialists</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <i class="fas fa-check-circle text-yellow-500 text-xl"></i>
                        <span class="text-gray-200">Private Dining Rooms Available</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <i class="fas fa-check-circle text-yellow-500 text-xl"></i>
                        <span class="text-gray-200">24/7 In-Room Dining Service</span>
                    </div>
                </div>
                <a href="/restaurant" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black rounded-full font-semibold hover:shadow-lg transition">
                    Explore Menu <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600" alt="Restaurant" class="rounded-2xl h-64 w-full object-cover">
                <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=600" alt="Food" class="rounded-2xl h-64 w-full object-cover mt-8">
                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600" alt="Dining" class="rounded-2xl h-64 w-full object-cover -mt-8">
                <img src="https://images.unsplash.com/photo-1533777857889-4be7c70b33f7?w=600" alt="Wine" class="rounded-2xl h-64 w-full object-cover">
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-gradient-to-r from-yellow-500/5 to-transparent">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <div class="inline-block px-6 py-2 glass-card rounded-full mb-4">
                <span class="text-yellow-500 text-sm font-semibold">GUEST TESTIMONIALS</span>
            </div>
            <h2 class="font-serif text-4xl md:text-5xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-4">What Our Guests Say</h2>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto">
                Real experiences from our valued guests
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="glass-card rounded-2xl p-6">
                <i class="fas fa-quote-left text-3xl text-yellow-500 mb-4 opacity-50"></i>
                <p class="text-gray-300 mb-4 italic leading-relaxed">"Absolutely magnificent! The service was impeccable, and the room was breathtaking. Best hotel experience of my life. Will definitely return!"</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-black text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white">John Anderson</h4>
                        <div class="flex text-yellow-500 text-sm">
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="glass-card rounded-2xl p-6">
                <i class="fas fa-quote-left text-3xl text-yellow-500 mb-4 opacity-50"></i>
                <p class="text-gray-300 mb-4 italic leading-relaxed">"The presidential suite exceeded our expectations. The view was incredible and the staff went above and beyond to make our honeymoon special."</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-black text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white">Sarah Williams</h4>
                        <div class="flex text-yellow-500 text-sm">
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="glass-card rounded-2xl p-6">
                <i class="fas fa-quote-left text-3xl text-yellow-500 mb-4 opacity-50"></i>
                <p class="text-gray-300 mb-4 italic leading-relaxed">"World-class facilities and outstanding service. The spa was incredibly relaxing. The food at the restaurant was exquisite. 10/10!"</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-black text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white">Michael Chen</h4>
                        <div class="flex text-yellow-500 text-sm">
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                            <i class="fas fa-star text-yellow-500"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Special Offer Banner -->
<section class="py-20">
    <div class="container mx-auto px-6">
        <div class="relative rounded-3xl overflow-hidden h-96">
            <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1600" alt="Special Offer" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-transparent flex items-center">
                <div class="px-8 md:px-16 max-w-2xl">
                    <div class="inline-block px-6 py-2 bg-white/10 backdrop-blur rounded-full mb-4 border border-yellow-500/30">
                        <span class="text-yellow-500 text-sm font-semibold">🔥 LIMITED TIME OFFER</span>
                    </div>
                    <h2 class="font-serif text-4xl md:text-5xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-4">Stay 3 Nights,<br>Get 1 Free</h2>
                    <p class="text-gray-200 mb-6 text-lg">Book your luxury getaway today and enjoy exclusive benefits including complimentary breakfast, spa access, and airport transfer.</p>
                    <div class="flex gap-4">
                        <a href="/rooms" class="px-8 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black rounded-full font-semibold hover:shadow-lg transition">
                            Claim Offer
                        </a>
                        <a href="#" class="px-8 py-3 glass-card text-white rounded-full font-semibold hover:bg-white/10 transition">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Subscription -->
<section class="py-20">
    <div class="container mx-auto px-6">
        <div class="glass-card rounded-3xl p-12 text-center">
            <i class="fas fa-envelope-open-text text-5xl text-yellow-500 mb-4"></i>
            <h2 class="font-serif text-3xl md:text-4xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-4">Subscribe to Our Newsletter</h2>
            <p class="text-gray-300 mb-6 max-w-2xl mx-auto">Get exclusive offers, updates, and special discounts directly to your inbox. Be the first to know about our promotions!</p>
            <form class="flex flex-col sm:flex-row gap-4 max-w-2xl mx-auto">
                <input type="email" placeholder="Your email address" class="flex-1 bg-gray-800 border border-yellow-500/30 rounded-full px-6 py-3 focus:outline-none focus:border-yellow-500 text-white">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black rounded-full font-semibold hover:shadow-lg transition">
                    Subscribe
                </button>
            </form>
            <p class="text-gray-500 text-sm mt-4">We respect your privacy. Unsubscribe at any time.</p>
        </div>
    </div>
</section>

<!-- Location & Map -->
<section class="py-20">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <div class="inline-block px-6 py-2 glass-card rounded-full mb-4">
                <span class="text-yellow-500 text-sm font-semibold">FIND US</span>
            </div>
            <h2 class="font-serif text-4xl md:text-5xl text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-yellow-600 mb-4">Our Location</h2>
            <p class="text-gray-300 text-lg max-w-2xl mx-auto">
                Located in the heart of the city, Val Royale offers easy access to premium shopping, entertainment, and business districts
            </p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-12">
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-2xl text-yellow-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-lg mb-1">Address</h3>
                        <p class="text-gray-300">123 Luxury Avenue, Downtown City Center, 12345</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone-alt text-2xl text-yellow-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-lg mb-1">Phone</h3>
                        <p class="text-gray-300">+62 123 4567 890</p>
                        <p class="text-gray-400 text-sm">Available 24/7</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-2xl text-yellow-500"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-lg mb-1">Email</h3>
                        <p class="text-gray-300">reservations@valroyale.com</p>
                        <p class="text-gray-400 text-sm">info@valroyale.com</p>
                    </div>
                </div>
            </div>
            <div class="h-96 rounded-2xl overflow-hidden shadow-xl">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322955!2d106.819595!3d-6.208763!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sJakarta!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<style>
    /* Date Picker Styling for Dark Mode */
    .date-input {
        color-scheme: dark;
        background-color: #1f2937 !important;
        color: white !important;
    }
    
    .date-input::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
        opacity: 1;
    }
    
    .date-input::-webkit-calendar-picker-indicator:hover {
        filter: invert(0.8);
    }
    
    .date-input::-webkit-datetime-edit-month-field,
    .date-input::-webkit-datetime-edit-day-field,
    .date-input::-webkit-datetime-edit-year-field {
        color: white;
    }
    
    .date-input::-webkit-datetime-edit-text {
        color: #9ca3af;
    }
    
    /* Select option styling */
    select {
        background-color: #1f2937;
        color: white;
    }
    
    select option {
        background-color: #1f2937;
        color: white;
    }
</style>
@endsection

@push('scripts')
<script>
    // Set default dates
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    
    if (checkInInput) {
        // Set default value and min date
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        checkInInput.value = `${year}-${month}-${day}`;
        checkInInput.min = `${year}-${month}-${day}`;
    }
    
    if (checkOutInput) {
        // Set default value for tomorrow
        const year = tomorrow.getFullYear();
        const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const day = String(tomorrow.getDate()).padStart(2, '0');
        checkOutInput.value = `${year}-${month}-${day}`;
        checkOutInput.min = `${year}-${month}-${day}`;
    }
    
    // Update check_out min when check_in changes
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const minCheckOut = new Date(checkInDate);
            minCheckOut.setDate(minCheckOut.getDate() + 1);
            
            const year = minCheckOut.getFullYear();
            const month = String(minCheckOut.getMonth() + 1).padStart(2, '0');
            const day = String(minCheckOut.getDate()).padStart(2, '0');
            checkOutInput.min = `${year}-${month}-${day}`;
            
            if (new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = `${year}-${month}-${day}`;
            }
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Animate on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Set initial styles for animation
    document.querySelectorAll('.glass-card, .room-card, .grid > div').forEach(el => {
        if (!el.classList.contains('glass-card') || !el.closest('.grid')) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        }
    });

     // Load last search from localStorage for auto-fill
    document.addEventListener('DOMContentLoaded', function() {
        const lastSearch = localStorage.getItem('lastSearch');
        if (lastSearch) {
            try {
                const search = JSON.parse(lastSearch);
                if (search.check_in && document.getElementById('check_in')) {
                    document.getElementById('check_in').value = search.check_in;
                }
                if (search.check_out && document.getElementById('check_out')) {
                    document.getElementById('check_out').value = search.check_out;
                }
                if (search.guests && document.getElementById('guests')) {
                    document.getElementById('guests').value = search.guests;
                }
                // Clear after loading to prevent stale data
                localStorage.removeItem('lastSearch');
            } catch(e) {
                console.log('Error loading last search:', e);
            }
        }
    });
    
    // Save search data to localStorage when form is submitted
    const availabilityForm = document.getElementById('availabilityForm');
    if (availabilityForm) {
        availabilityForm.addEventListener('submit', function() {
            const checkIn = document.getElementById('check_in')?.value;
            const checkOut = document.getElementById('check_out')?.value;
            const guests = document.getElementById('guests')?.value;
            
            if (checkIn && checkOut && guests) {
                localStorage.setItem('lastSearch', JSON.stringify({
                    check_in: checkIn,
                    check_out: checkOut,
                    guests: guests
                }));
            }
        });
    }
</script>
@endpush