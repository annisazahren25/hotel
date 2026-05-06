<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Check-in/out time
            $table->time('check_in_time')->default('14:00:00')->after('check_in_date');
            $table->time('check_out_time')->default('12:00:00')->after('check_out_date');
            
            // Early check-in / Late check-out
            $table->enum('early_checkin', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('check_out_time');
            $table->enum('late_checkout', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('early_checkin');
            $table->decimal('early_checkin_fee', 12, 2)->default(0)->after('late_checkout');
            $table->decimal('late_checkout_fee', 12, 2)->default(0)->after('early_checkin_fee');
            
            // Payment & Tax
            $table->string('payment_proof')->nullable()->after('total_price');
            $table->timestamp('paid_at')->nullable()->after('payment_proof');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('total_price');
            $table->decimal('grand_total', 12, 2)->after('tax_amount');
            
            // Cancellation
            $table->timestamp('cancelled_at')->nullable()->after('paid_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->enum('cancellation_status', ['pending', 'approved', 'rejected'])->default('pending')->after('cancellation_reason');
            $table->timestamp('cancellation_approved_at')->nullable()->after('cancellation_status');
            $table->foreignId('cancelled_by')->nullable()->after('cancellation_approved_at');
            
            // Refund
            $table->decimal('refund_amount', 12, 2)->default(0)->after('cancellation_reason');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_amount');
            $table->string('refund_proof')->nullable()->after('refund_processed_at');
            $table->text('admin_notes')->nullable()->after('refund_proof');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_time', 'check_out_time', 'early_checkin', 'late_checkout',
                'early_checkin_fee', 'late_checkout_fee', 'payment_proof', 'paid_at',
                'tax_amount', 'grand_total', 'cancelled_at', 'cancellation_reason',
                'cancellation_status', 'cancellation_approved_at', 'cancelled_by',
                'refund_amount', 'refund_processed_at', 'refund_proof', 'admin_notes'
            ]);
        });
    }
};