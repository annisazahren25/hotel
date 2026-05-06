protected $routeMiddleware = [
    // ... existing
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];

protected $routeMiddleware = [
    // ... existing
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];