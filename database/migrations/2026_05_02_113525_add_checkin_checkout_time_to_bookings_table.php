<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('check_in_time')->default('14:00:00')->after('check_in_date');
            $table->time('check_out_time')->default('12:00:00')->after('check_out_date');
            $table->enum('early_checkin', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('check_out_time');
            $table->enum('late_checkout', ['none', 'requested', 'approved', 'rejected'])->default('none')->after('early_checkin');
            $table->decimal('early_checkin_fee', 12, 2)->default(0)->after('late_checkout');
            $table->decimal('late_checkout_fee', 12, 2)->default(0)->after('early_checkin_fee');
            $table->decimal('refund_amount', 12, 2)->default(0)->after('cancellation_reason');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_amount');
            $table->enum('cancellation_type', ['user', 'admin', 'system', 'no_show'])->nullable()->after('cancellation_reason');
            $table->text('admin_notes')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_time', 'check_out_time', 'early_checkin', 'late_checkout',
                'early_checkin_fee', 'late_checkout_fee', 'refund_amount',
                'refund_processed_at', 'cancellation_type', 'admin_notes'
            ]);
        });
    }
};