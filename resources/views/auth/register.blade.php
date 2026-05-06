@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="max-w-md mx-auto">
    <div class="glass-card rounded-2xl p-8">
        <div class="text-center mb-8">
            <i class="fas fa-crown text-5xl gold-text mb-4"></i>
            <h2 class="font-serif text-3xl gold-text">Join Val Royale</h2>
            <p class="text-gray-400 mt-2">Create your account</p>
        </div>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500 @error('name') border-red-500 @enderror" 
                       required autofocus>
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500 @error('email') border-red-500 @enderror" 
                       required>
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
            
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" 
                       class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500" 
                       required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">No. Telepon (Opsional)</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" 
                       class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">No. Identitas (Opsional)</label>
                <input type="text" name="identity_number" value="{{ old('identity_number') }}" 
                       class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Alamat (Opsional)</label>
                <textarea name="address" rows="3" 
                          class="w-full bg-white/5 border border-gold-500/30 rounded-lg px-4 py-3 focus:outline-none focus:border-gold-500">{{ old('address') }}</textarea>
            </div>
            
            <button type="submit" class="w-full btn-gold text-black font-semibold py-3 rounded-lg">
                Register
            </button>
        </form>
        
        <p class="text-center text-gray-400 mt-6">
            Already have an account? <a href="{{ route('login') }}" class="gold-text">Login here</a>
        </p>
    </div>
</div>
@endsection