<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add cancellation fields
            $table->timestamp('cancellation_requested_at')->nullable();
            $table->timestamp('cancellation_approved_at')->nullable();
            $table->timestamp('cancellation_rejected_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('cancellation_admin_note')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->timestamp('refund_processed_at')->nullable();
            
            // Add guests column if not exists
            if (!Schema::hasColumn('bookings', 'guests')) {
                $table->integer('guests')->default(2);
            }
            
            // Add special_requests column if not exists
            if (!Schema::hasColumn('bookings', 'special_requests')) {
                $table->text('special_requests')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_requested_at',
                'cancellation_approved_at',
                'cancellation_rejected_at',
                'cancellation_reason',
                'cancellation_admin_note',
                'refund_amount',
                'refund_processed_at'
            ]);
        });
    }
};