<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambah
            if (!Schema::hasColumn('bookings', 'booking_code')) {
                $table->string('booking_code')->nullable()->unique()->after('id');
            }
            
            if (!Schema::hasColumn('bookings', 'number_of_guests')) {
                $table->integer('number_of_guests')->default(1)->after('check_out_date');
            }
            
            if (!Schema::hasColumn('bookings', 'special_requests')) {
                $table->text('special_requests')->nullable()->after('number_of_guests');
            }
            
            if (!Schema::hasColumn('bookings', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0)->after('total_price');
            }
            
            if (!Schema::hasColumn('bookings', 'final_amount')) {
                $table->decimal('final_amount', 12, 2)->after('discount_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_code',
                'number_of_guests',
                'special_requests',
                'discount_amount',
                'final_amount'
            ]);
        });
    }
};