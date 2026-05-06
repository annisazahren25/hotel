<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\RestaurantMenu;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== USERS ====================
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
        
        // Customers
        Guest::create([
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'password' => Hash::make('customer123'),
            'phone' => '081234567895',
            'role' => 'customer'
        ]);
        
        Guest::create([
            'name' => 'Annisa',
            'email' => 'annisa@gmail.com',
            'password' => Hash::make('annisa123'),
            'phone' => '081234567896',
            'role' => 'customer'
        ]);
        
        // ==================== ROOM TYPES ====================
        $roomTypes = [
            [
                'name' => 'Standard Room',
                'description' => 'Comfortable room with city view, perfect for business travelers',
                'price' => 800000,
                'capacity' => 2,
                'photo_url' => 'https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?w=400'
            ],
            [
                'name' => 'Deluxe Suite',
                'description' => 'Luxurious suite with panoramic city views and marble bathroom',
                'price' => 1500000,
                'capacity' => 2,
                'photo_url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400'
            ],
            [
                'name' => 'Executive Suite',
                'description' => 'Executive suite with separate living room and workspace',
                'price' => 2500000,
                'capacity' => 3,
                'photo_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400'
            ],
            [
                'name' => 'Family Suites',
                'description' => 'Spacious suite perfect for family with 2 bedrooms',
                'price' => 2000000,
                'capacity' => 5,
                'photo_url' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400'
            ],
            [
                'name' => 'Presidential Suite',
                'description' => 'Ultimate luxury with private terrace, jacuzzi, and butler service',
                'price' => 5000000,
                'capacity' => 4,
                'photo_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400'
            ],
        ];
        
        foreach ($roomTypes as $type) {
            $roomType = RoomType::create($type);
            
            // Create rooms for each room type (5 rooms per type)
            for ($i = 1; $i <= 5; $i++) {
                $roomNumber = $roomType->id . '0' . $i;
                Room::create([
                    'room_type_id' => $roomType->id,
                    'room_number' => $roomNumber,
                    'status' => 'available',
                    'floor' => $roomType->id,
                    'photo_url' => $type['photo_url']
                ]);
            }
        }
        
        // ==================== RESTAURANT MENU ====================
        $menus = [
            [
                'name' => 'Wagyu Steak',
                'description' => 'Premium Japanese Wagyu A5 with truffle sauce',
                'price' => 450000,
                'category' => 'Main Course',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1558030006-45067539346d?w=400'
            ],
            [
                'name' => 'Lobster Thermidor',
                'description' => 'Fresh lobster with creamy cheese sauce',
                'price' => 350000,
                'category' => 'Seafood',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=400'
            ],
            [
                'name' => 'Truffle Pasta',
                'description' => 'Handmade pasta with black truffle and parmesan',
                'price' => 180000,
                'category' => 'Pasta',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=400'
            ],
            [
                'name' => 'Caesar Salad',
                'description' => 'Fresh romaine lettuce with parmesan and croutons',
                'price' => 85000,
                'category' => 'Appetizer',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=400'
            ],
            [
                'name' => 'Chocolate Lava Cake',
                'description' => 'Warm chocolate cake with melting center',
                'price' => 65000,
                'category' => 'Dessert',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=400'
            ],
            [
                'name' => 'Nasi Goreng Special',
                'description' => 'Indonesian fried rice with prawn and chicken',
                'price' => 75000,
                'category' => 'Main Course',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400'
            ],
            [
                'name' => 'Green Salad',
                'description' => 'Mixed fresh greens with balsamic dressing',
                'price' => 55000,
                'category' => 'Appetizer',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400'
            ],
            [
                'name' => 'Orange Juice',
                'description' => 'Fresh squeezed orange juice',
                'price' => 35000,
                'category' => 'Beverage',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1600271886742-f2d4c3e0a2e8?w=400'
            ],
            [
                'name' => 'Red Wine',
                'description' => 'Premium red wine from France',
                'price' => 250000,
                'category' => 'Beverage',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?w=400'
            ],
            [
                'name' => 'Matcha Latte',
                'description' => 'Japanese matcha with fresh milk',
                'price' => 45000,
                'category' => 'Beverage',
                'is_available' => true,
                'photo_url' => 'https://images.unsplash.com/photo-1534778566635-dc947cd1281c?w=400'
            ],
        ];
        
        foreach ($menus as $menu) {
            RestaurantMenu::create($menu);
        }
        
        // ==================== SUMMARY ====================
        $this->command->info('========================================');
        $this->command->info('✅ DATABASE SEEDED SUCCESSFULLY!');
        $this->command->info('========================================');
        $this->command->info("📊 Statistics:");
        $this->command->info("   - Users: " . Guest::count());
        $this->command->info("   - Room Types: " . RoomType::count());
        $this->command->info("   - Rooms: " . Room::count());
        $this->command->info("   - Menu Items: " . RestaurantMenu::count());
        $this->command->info('========================================');
        $this->command->info('🔐 LOGIN CREDENTIALS:');
        $this->command->info('   Super Admin: superadmin@valroyale.com / superadmin123');
        $this->command->info('   Admin: admin@valroyale.com / admin123');
        $this->command->info('   Customer: annisa@gmail.com / annisa123');
        $this->command->info('========================================');
    }
}