<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CancellationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== HOME ROUTE ====================
Route::get('/', function () {
    return view('home');
})->name('home');

// ==================== AUTHENTICATION ROUTES ====================
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

// ==================== PUBLIC ROUTES ====================
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{id}', [RoomController::class, 'detail'])->name('rooms.detail');
Route::post('/rooms/check-availability', [RoomController::class, 'checkAvailability'])->name('rooms.check');

// ==================== AUTHENTICATED USER ROUTES ====================
Route::middleware('auth')->group(function () {
    
    // ==================== BOOKING ROUTES ====================
    Route::prefix('bookings')->controller(BookingController::class)->group(function () {
        Route::get('/create', 'create')->name('bookings.create');
        Route::post('/', 'store')->name('bookings.store');
        Route::get('/summary/{id}', 'summary')->name('booking.summary');
        Route::get('/my-bookings', 'myBookings')->name('my.bookings');
        Route::get('/{id}', 'show')->name('booking.show');
        Route::post('/upload-proof/{id}', 'uploadProof')->name('booking.upload.proof');
        Route::post('/{id}/cancel', 'cancel')->name('bookings.cancel');
        Route::get('/{id}/cancellation-details', 'getCancellationDetails')->name('bookings.cancellation-details');
    });
    
    // ==================== CANCELLATION ROUTES ====================
    Route::prefix('cancellation')->controller(CancellationController::class)->group(function () {
        Route::get('/{id}', 'showForm')->name('cancellation.form');
        Route::post('/{id}/request', 'requestCancellation')->name('cancellation.request');
        Route::post('/{id}/approve', 'approveCancellation')->name('cancellation.approve');
        Route::post('/{id}/reject', 'rejectCancellation')->name('cancellation.reject');
        Route::post('/{id}/refund', 'processRefund')->name('cancellation.refund');
    });
    
    // ==================== RESTAURANT ROUTES (CUSTOMER) ====================
    Route::prefix('restaurant')->controller(RestaurantController::class)->group(function () {
        Route::get('/', 'menu')->name('restaurant.menu');
        Route::get('/cart', 'viewCart')->name('cart.view');
        Route::post('/cart/add', 'addToCart')->name('cart.add');
        Route::post('/cart/update', 'updateCart')->name('cart.update');
        Route::delete('/cart/remove/{id}', 'removeFromCart')->name('cart.remove');
        Route::get('/cart/count', 'getCartCount')->name('cart.count');
        Route::post('/checkout', 'checkout')->name('restaurant.checkout');
        Route::get('/my-orders', 'myOrders')->name('restaurant.orders');
        Route::get('/my-orders/{id}', 'orderDetail')->name('restaurant.order.detail');
        Route::get('/track/{id}', 'trackOrder')->name('restaurant.order.track');
        Route::post('/cancel/{id}', 'cancelOrder')->name('restaurant.order.cancel');
        Route::get('/status/{id}', 'getOrderStatus')->name('restaurant.order.status');
    });
    
    // ==================== PAYMENT ROUTES ====================
    Route::prefix('payment')->controller(PaymentController::class)->group(function () {
        // Main payment pages
        Route::get('/booking/{id}', 'payBooking')->name('payment.booking');
        Route::post('/booking/{id}', 'processBookingPayment')->name('payment.booking.process');
        
        // Cash Payment Routes
        Route::get('/cash/{id}', 'showCashPayment')->name('payment.cash');
        Route::post('/cash/{id}/process', 'processCashPayment')->name('payment.cash.process');
        
        // Bank Transfer Payment Routes
        Route::get('/transfer/{id}', 'showTransferPayment')->name('payment.transfer');
        Route::post('/transfer/{id}/process', 'processTransferPayment')->name('payment.transfer.process');
        
        // Credit Card Payment Routes
        Route::get('/credit-card/{id}', 'showCreditCardPayment')->name('payment.credit-card');
        Route::post('/credit-card/{id}/process', 'processCreditCardPayment')->name('payment.credit-card.process');
        
        // E-Wallet Payment Routes
        Route::get('/ewallet/{id}', 'showEwalletPayment')->name('payment.ewallet');
        Route::post('/ewallet/{id}/process', 'processEwalletPayment')->name('payment.ewallet.process');
        
        // Restaurant Payment
        Route::get('/restaurant/{id}', 'payRestaurant')->name('payment.restaurant');
        Route::post('/restaurant/{id}', 'processRestaurantPayment')->name('payment.restaurant.process');
        
        // Payment History & Details
        Route::get('/history', 'myPayments')->name('payment.history');
        Route::get('/{id}', 'show')->name('payment.show');
    });
});

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Room Types Management
    Route::prefix('room-types')->controller(App\Http\Controllers\Admin\RoomTypeController::class)->group(function () {
        Route::get('/', 'index')->name('admin.room-types.index');
        Route::get('/create', 'create')->name('admin.room-types.create');
        Route::post('/', 'store')->name('admin.room-types.store');
        Route::get('/{id}/edit', 'edit')->name('admin.room-types.edit');
        Route::put('/{id}', 'update')->name('admin.room-types.update');
        Route::delete('/{id}', 'destroy')->name('admin.room-types.destroy');
        Route::get('/details/{id}', 'getRoomTypeDetails')->name('admin.room-types.details');
        Route::post('/bulk-delete', 'bulkDelete')->name('admin.room-types.bulk-delete');
        Route::get('/export', 'export')->name('admin.room-types.export');
        Route::get('/statistics', 'getStatistics')->name('admin.room-types.statistics');
    });
    
    // Room Management
    Route::prefix('rooms')->controller(App\Http\Controllers\Admin\RoomController::class)->group(function () {
        Route::get('/', 'index')->name('admin.rooms.index');
        Route::get('/create', 'create')->name('admin.rooms.create');
        Route::post('/', 'store')->name('admin.rooms.store');
        Route::get('/{id}/edit', 'edit')->name('admin.rooms.edit');
        Route::put('/{id}', 'update')->name('admin.rooms.update');
        Route::delete('/{id}', 'destroy')->name('admin.rooms.destroy');
        Route::post('/{id}/status', 'updateStatus')->name('admin.rooms.status');
        Route::get('/floor/{floor}', 'getRoomsByFloor')->name('admin.rooms.by-floor');
        Route::get('/details/{id}', 'getRoomDetails')->name('admin.rooms.details');
        Route::post('/clone/{id}', 'clone')->name('admin.rooms.clone');
        Route::get('/export', 'export')->name('admin.rooms.export');
        Route::get('/statistics', 'getStatistics')->name('admin.rooms.statistics');
        Route::post('/bulk-status', 'bulkUpdateStatus')->name('admin.rooms.bulk-status');
    });
    
    // Booking Management
    Route::prefix('bookings')->controller(App\Http\Controllers\Admin\BookingController::class)->group(function () {
        Route::get('/', 'index')->name('admin.bookings.index');
        Route::get('/export', 'export')->name('admin.bookings.export');
        Route::get('/statistics', 'getStatistics')->name('admin.bookings.statistics');
        Route::get('/{id}', 'show')->name('admin.bookings.show');
        Route::post('/{id}/status', 'updateStatus')->name('admin.bookings.status');
        Route::post('/{id}/checkin', 'checkIn')->name('admin.bookings.checkin');
        Route::post('/{id}/checkout', 'checkOut')->name('admin.bookings.checkout');
        Route::delete('/{id}', 'destroy')->name('admin.bookings.destroy');
    });
    
    // Cancellation Management
    Route::prefix('cancellations')->controller(App\Http\Controllers\Admin\CancellationController::class)->group(function () {
        Route::get('/', 'index')->name('admin.cancellations.index');
        Route::get('/{id}/show', 'show')->name('admin.cancellations.show');
        Route::post('/{id}/approve', 'approve')->name('admin.cancellations.approve');
        Route::post('/{id}/reject', 'reject')->name('admin.cancellations.reject');
    });
    
    // Restaurant Management (Admin)
    Route::prefix('restaurant')->controller(App\Http\Controllers\Admin\RestaurantController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('admin.restaurant.dashboard');
        Route::get('/orders', 'orders')->name('admin.restaurant.orders');
        Route::get('/orders/export', 'exportOrders')->name('admin.restaurant.export-orders');
        Route::get('/orders/statistics', 'getStatistics')->name('admin.restaurant.statistics');
        Route::get('/orders/{id}', 'orderDetail')->name('admin.restaurant.order.detail');
        Route::post('/orders/{id}/status', 'updateOrderStatus')->name('admin.restaurant.order.status');
        Route::post('/orders/bulk-status', 'bulkUpdateStatus')->name('admin.restaurant.bulk-status');
        Route::get('/orders/{id}/print', 'printOrder')->name('admin.restaurant.order.print');
        
        // Menu Management
        Route::get('/menu', 'menu')->name('admin.restaurant.menu');
        Route::get('/menu/create', 'createMenu')->name('admin.restaurant.menu.create');
        Route::post('/menu', 'storeMenu')->name('admin.restaurant.menu.store');
        Route::get('/menu/{id}/edit', 'editMenu')->name('admin.restaurant.menu.edit');
        Route::put('/menu/{id}', 'updateMenu')->name('admin.restaurant.menu.update');
        Route::delete('/menu/{id}', 'destroyMenu')->name('admin.restaurant.menu.destroy');
        Route::post('/menu/{id}/toggle', 'toggleAvailability')->name('admin.restaurant.menu.toggle');
        Route::get('/menu/export', 'exportMenu')->name('admin.restaurant.menu.export');
    });
    
    // Guest/Customer Management
    Route::prefix('guests')->controller(App\Http\Controllers\Admin\GuestController::class)->group(function () {
        Route::get('/', 'index')->name('admin.guests.index');
        Route::get('/export', 'export')->name('admin.guests.export');
        Route::get('/statistics', 'getStatistics')->name('admin.guests.statistics');
        Route::get('/{id}', 'show')->name('admin.guests.show');
        Route::post('/{id}/upgrade', 'upgrade')->name('admin.guests.upgrade');
        Route::delete('/{id}', 'destroy')->name('admin.guests.destroy');
    });
    
    // Staff Management (Super Admin only)
    Route::prefix('staff')->middleware(['role:super_admin'])->controller(App\Http\Controllers\Admin\StaffController::class)->group(function () {
        Route::get('/', 'index')->name('admin.staff.index');
        Route::get('/create', 'create')->name('admin.staff.create');
        Route::post('/', 'store')->name('admin.staff.store');
        Route::get('/{id}/edit', 'edit')->name('admin.staff.edit');
        Route::put('/{id}', 'update')->name('admin.staff.update');
        Route::delete('/{id}', 'destroy')->name('admin.staff.destroy');
        Route::post('/{id}/reset-password', 'resetPassword')->name('admin.staff.reset-password');
    });
    
    // Payment Management (Admin)
    Route::prefix('payments')->controller(App\Http\Controllers\Admin\PaymentController::class)->group(function () {
        Route::get('/', 'index')->name('admin.payments.index');
        Route::get('/export', 'export')->name('admin.payments.export');
        Route::get('/statistics', 'getStatistics')->name('admin.payments.statistics');
        Route::get('/{id}', 'show')->name('admin.payments.show');
        Route::post('/{id}/refund', 'refund')->name('admin.payments.refund');
        Route::post('/{id}/verify', 'verify')->name('admin.payments.verify');
    });
    
    // Reports (Admin & Super Admin only)
    Route::prefix('reports')->middleware(['role:super_admin,admin'])->controller(App\Http\Controllers\Admin\ReportController::class)->group(function () {
        Route::get('/', 'index')->name('admin.reports.index');
        Route::get('/revenue', 'revenue')->name('admin.reports.revenue');
        Route::get('/occupancy', 'occupancy')->name('admin.reports.occupancy');
        Route::get('/restaurant', 'restaurant')->name('admin.reports.restaurant');
        Route::get('/export/{type}', 'export')->name('admin.reports.export');
        Route::get('/download/{file}', 'download')->name('admin.reports.download');
    });
    
    // Settings (Super Admin only)
    Route::prefix('settings')->middleware(['role:super_admin'])->controller(App\Http\Controllers\Admin\SettingController::class)->group(function () {
        Route::get('/', 'index')->name('admin.settings');
        Route::post('/general', 'updateGeneral')->name('admin.settings.general');
        Route::post('/payment', 'updatePayment')->name('admin.settings.payment');
        Route::post('/notification', 'updateNotification')->name('admin.settings.notification');
        Route::post('/backup', 'backup')->name('admin.settings.backup');
        Route::post('/cache/clear', 'clearCache')->name('admin.settings.clear-cache');
    });
});

// ==================== STAFF SPECIFIC ROUTES ====================
Route::prefix('staff')->middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/check-ins', [App\Http\Controllers\Staff\CheckInController::class, 'index'])->name('staff.checkins');
    Route::post('/check-in/{id}', [App\Http\Controllers\Staff\CheckInController::class, 'processCheckIn'])->name('staff.checkin.process');
    Route::post('/check-out/{id}', [App\Http\Controllers\Staff\CheckInController::class, 'processCheckOut'])->name('staff.checkout.process');
    Route::get('/rooms', [App\Http\Controllers\Staff\RoomController::class, 'index'])->name('staff.rooms');
    Route::get('/rooms/{id}', [App\Http\Controllers\Staff\RoomController::class, 'show'])->name('staff.rooms.show');
    Route::post('/rooms/{id}/status', [App\Http\Controllers\Staff\RoomController::class, 'updateStatus'])->name('staff.rooms.status');
    Route::get('/bookings/today', [App\Http\Controllers\Staff\BookingController::class, 'today'])->name('staff.bookings.today');
});

// ==================== RESTAURANT STAFF SPECIFIC ROUTES ====================
Route::prefix('restaurant-staff')->middleware(['auth', 'role:restaurant'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\RestaurantStaff\DashboardController::class, 'index'])->name('restaurant-staff.dashboard');
    Route::get('/orders', [App\Http\Controllers\RestaurantStaff\OrderController::class, 'index'])->name('restaurant-staff.orders');
    Route::get('/orders/export', [App\Http\Controllers\RestaurantStaff\OrderController::class, 'export'])->name('restaurant-staff.orders.export');
    Route::get('/orders/{id}', [App\Http\Controllers\RestaurantStaff\OrderController::class, 'show'])->name('restaurant-staff.order.show');
    Route::post('/orders/{id}/status', [App\Http\Controllers\RestaurantStaff\OrderController::class, 'updateStatus'])->name('restaurant-staff.order.status');
    Route::post('/orders/bulk-status', [App\Http\Controllers\RestaurantStaff\OrderController::class, 'bulkUpdate'])->name('restaurant-staff.orders.bulk');
    Route::get('/menu', [App\Http\Controllers\RestaurantStaff\MenuController::class, 'index'])->name('restaurant-staff.menu');
    Route::post('/menu/{id}/toggle', [App\Http\Controllers\RestaurantStaff\MenuController::class, 'toggleAvailability'])->name('restaurant-staff.menu.toggle');
});

// ==================== HOUSEKEEPING STAFF ROUTES ====================
Route::prefix('housekeeping-staff')->middleware(['auth', 'role:housekeeping'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HousekeepingStaff\DashboardController::class, 'index'])->name('housekeeping-staff.dashboard');
    Route::get('/rooms', [App\Http\Controllers\HousekeepingStaff\RoomController::class, 'index'])->name('housekeeping-staff.rooms');
    Route::get('/rooms/assigned', [App\Http\Controllers\HousekeepingStaff\RoomController::class, 'assigned'])->name('housekeeping-staff.rooms.assigned');
    Route::post('/rooms/{id}/clean', [App\Http\Controllers\HousekeepingStaff\RoomController::class, 'markClean'])->name('housekeeping-staff.room.clean');
    Route::post('/rooms/{id}/report', [App\Http\Controllers\HousekeepingStaff\RoomController::class, 'reportIssue'])->name('housekeeping-staff.room.report');
    Route::post('/tasks/{id}/complete', [App\Http\Controllers\HousekeepingStaff\TaskController::class, 'complete'])->name('housekeeping-staff.task.complete');
    Route::get('/schedule', [App\Http\Controllers\HousekeepingStaff\ScheduleController::class, 'index'])->name('housekeeping-staff.schedule');
});

// ==================== API ROUTES (AJAX) ====================
Route::prefix('api')->middleware('auth')->group(function () {
    // Dashboard API
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats'])->name('api.dashboard.stats');
    Route::get('/dashboard/charts', [App\Http\Controllers\Api\DashboardController::class, 'charts'])->name('api.dashboard.charts');
    Route::get('/dashboard/notifications', [App\Http\Controllers\Api\DashboardController::class, 'notifications'])->name('api.dashboard.notifications');
    
    // Rooms API
    Route::get('/rooms/available', [App\Http\Controllers\Api\RoomController::class, 'available'])->name('api.rooms.available');
    Route::get('/rooms/search', [App\Http\Controllers\Api\RoomController::class, 'search'])->name('api.rooms.search');
    Route::get('/rooms/status', [App\Http\Controllers\Api\RoomController::class, 'status'])->name('api.rooms.status');
    
    // Bookings API
    Route::get('/bookings/today', [App\Http\Controllers\Api\BookingController::class, 'today'])->name('api.bookings.today');
    Route::get('/bookings/upcoming', [App\Http\Controllers\Api\BookingController::class, 'upcoming'])->name('api.bookings.upcoming');
    Route::get('/bookings/check-availability', [App\Http\Controllers\Api\BookingController::class, 'checkAvailability'])->name('api.bookings.check-availability');
    
    // Restaurant API (Customer side)
    Route::get('/restaurant/menu', [App\Http\Controllers\Api\RestaurantController::class, 'menu'])->name('api.restaurant.menu');
    Route::get('/restaurant/orders/status/{id}', [App\Http\Controllers\Api\RestaurantController::class, 'orderStatus'])->name('api.restaurant.order-status');
    
    // Notifications API
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index'])->name('api.notifications');
    Route::get('/notifications/unread', [App\Http\Controllers\Api\NotificationController::class, 'unread'])->name('api.notifications.unread');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllRead'])->name('api.notifications.read-all');
});

// ==================== ERROR HANDLING ====================
// Fallback route for 404
Route::fallback(function () {
    if (request()->is('api/*')) {
        return response()->json([
            'success' => false, 
            'message' => 'API endpoint not found',
            'status' => 404
        ], 404);
    }
    return view('errors.404');
});