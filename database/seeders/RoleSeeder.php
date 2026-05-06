<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        Guest::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@valroyale.com',
            'password' => Hash::make('superadmin123'),
            'phone' => '081234567890',
            'role' => 'super_admin'
        ]);
        
        // Admin
        Guest::create([
            'name' => 'Admin User',
            'email' => 'admin@valroyale.com',
            'password' => Hash::make('admin123'),
            'phone' => '081234567891',
            'role' => 'admin'
        ]);
        
        // Staff
        Guest::create([
            'name' => 'Staff User',
            'email' => 'staff@valroyale.com',
            'password' => Hash::make('staff123'),
            'phone' => '081234567892',
            'role' => 'staff'
        ]);
        
        // Housekeeping
        Guest::create([
            'name' => 'Housekeeping Staff',
            'email' => 'housekeeping@valroyale.com',
            'password' => Hash::make('house123'),
            'phone' => '081234567893',
            'role' => 'housekeeping'
        ]);
        
        // Restaurant Staff
        Guest::create([
            'name' => 'Restaurant Staff',
            'email' => 'restaurant@valroyale.com',
            'password' => Hash::make('resto123'),
            'phone' => '081234567894',
            'role' => 'restaurant'
        ]);
        
        // Customer
        Guest::create([
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '081234567895',
            'role' => 'customer'
        ]);
    }
}