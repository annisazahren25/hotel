<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_code')->unique()->nullable()->after('id');
            $table->integer('number_of_guests')->default(1)->after('check_out_date');
            $table->text('special_requests')->nullable()->after('number_of_guests');
            $table->time('check_in_time')->default('14:00:00')->after('check_in_date');
            $table->time('check_out_time')->default('12:00:00')->after('check_out_date');
            $table->enum('early_checkin', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('check_out_time');
            $table->enum('late_checkout', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('early_checkin');
            $table->decimal('early_checkin_fee', 12, 2)->default(0)->after('late_checkout');
            $table->decimal('late_checkout_fee', 12, 2)->default(0)->after('early_checkin_fee');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('total_price');
            $table->decimal('final_amount', 12, 2)->after('discount_amount');
            $table->string('payment_proof')->nullable()->after('final_amount');
            $table->timestamp('paid_at')->nullable()->after('payment_proof');
            $table->timestamp('cancelled_at')->nullable()->after('paid_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->string('cancellation_type')->nullable()->after('cancellation_reason');
            $table->decimal('refund_amount', 12, 2)->default(0)->after('cancellation_type');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_amount');
            $table->text('admin_notes')->nullable()->after('refund_processed_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_code', 'number_of_guests', 'special_requests',
                'check_in_time', 'check_out_time', 'early_checkin', 'late_checkout',
                'early_checkin_fee', 'late_checkout_fee', 'discount_amount', 'final_amount',
                'payment_proof', 'paid_at', 'cancelled_at', 'cancellation_reason',
                'cancellation_type', 'refund_amount', 'refund_processed_at', 'admin_notes'
            ]);
        });
    }
};