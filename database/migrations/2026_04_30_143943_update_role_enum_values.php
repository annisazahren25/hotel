<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing enum and recreate
        DB::statement("ALTER TABLE guests MODIFY COLUMN role ENUM('super_admin', 'admin', 'staff', 'housekeeping', 'restaurant', 'customer') DEFAULT 'customer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE guests MODIFY COLUMN role ENUM('customer', 'admin') DEFAULT 'customer'");
    }
};