<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Check and add columns if they don't exist
            if (!Schema::hasColumn('bookings', 'number_of_guests')) {
                $table->integer('number_of_guests')->default(1)->after('check_out_date');
            }
            
            if (!Schema::hasColumn('bookings', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0)->after('total_price');
            }
            
            if (!Schema::hasColumn('bookings', 'grand_total')) {
                $table->decimal('grand_total', 12, 2)->after('tax_amount');
            }
            
            if (!Schema::hasColumn('bookings', 'check_in_time')) {
                $table->time('check_in_time')->default('14:00:00')->after('check_in_date');
            }
            
            if (!Schema::hasColumn('bookings', 'check_out_time')) {
                $table->time('check_out_time')->default('12:00:00')->after('check_out_date');
            }
            
            if (!Schema::hasColumn('bookings', 'special_requests')) {
                $table->text('special_requests')->nullable()->after('number_of_guests');
            }
            
            if (!Schema::hasColumn('bookings', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('grand_total');
            }
            
            if (!Schema::hasColumn('bookings', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_proof');
            }
            
            if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('paid_at');
            }
            
            if (!Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }
            
            if (!Schema::hasColumn('bookings', 'cancellation_status')) {
                $table->enum('cancellation_status', ['pending', 'approved', 'rejected'])->default('pending')->after('cancellation_reason');
            }
            
            if (!Schema::hasColumn('bookings', 'cancellation_approved_at')) {
                $table->timestamp('cancellation_approved_at')->nullable()->after('cancellation_status');
            }
            
            if (!Schema::hasColumn('bookings', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->after('cancellation_approved_at');
            }
            
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 12, 2)->default(0)->after('cancellation_reason');
            }
            
            if (!Schema::hasColumn('bookings', 'refund_processed_at')) {
                $table->timestamp('refund_processed_at')->nullable()->after('refund_amount');
            }
            
            if (!Schema::hasColumn('bookings', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('refund_processed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = [
                'number_of_guests', 'tax_amount', 'grand_total', 'check_in_time', 
                'check_out_time', 'special_requests', 'payment_proof', 'paid_at',
                'cancelled_at', 'cancellation_reason', 'cancellation_status',
                'cancellation_approved_at', 'cancelled_by', 'refund_amount',
                'refund_processed_at', 'admin_notes'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};