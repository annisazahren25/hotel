<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }
    
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.register');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $remember = $request->has('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Redirect based on role
            return $this->redirectBasedOnRole();
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }
    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:guests,email',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string|max:15',
        ]);
        
        $guest = Guest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'customer', // Default role untuk registrasi
        ]);
        
        Auth::login($guest);
        
        return redirect()->route('home')->with('success', 'Registrasi berhasil! Selamat datang ' . $guest->name);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
    
    // Function to redirect based on user role
    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        switch ($user->role) {
            case 'super_admin':
            case 'admin':
                return redirect()->route('admin.dashboard');
                
            case 'staff':
                return redirect()->route('admin.dashboard');
                
            case 'housekeeping':
                return redirect()->route('admin.housekeeping');
                
            case 'restaurant':
                return redirect()->route('admin.restaurant.orders');
                
            case 'customer':
            default:
                return redirect()->route('home');
        }
    }
}