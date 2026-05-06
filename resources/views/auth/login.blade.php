@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto">
    <div class="glass-card rounded-2xl p-8">
        <div class="text-center mb-8">
            <i class="fas fa-crown text-5xl gold-text mb-4"></i>
            <h2 class="font-serif text-3xl gold-text">Welcome Back</h2>
            <p class="text-gray-400 mt-2">Login to your account</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500 @error('email') border-red-500 @enderror" 
                       required autofocus>
                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Password</label>
                <input type="password" name="password" 
                       class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500 @error('password') border-red-500 @enderror" 
                       required>
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            
            
            <button type="submit" class="w-full btn-gold text-black font-semibold py-3 rounded-lg">
                Login
            </button>
        </form>
        
        <p class="text-center text-gray-400 mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="gold-text">Register now</a>
        </p>
    </div>
</div>
@endsection