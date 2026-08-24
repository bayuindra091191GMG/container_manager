<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));


            //Admin
            Route::middleware('web')
                ->group(base_path('routes/admin/hierarchy.php'));
            Route::middleware('web')
                ->group(base_path('routes/admin/access.php'));
            Route::middleware('web')
                ->group(base_path('routes/admin/user.php'));
            Route::middleware('web')
                ->group(base_path('routes/admin/dashboard.php'));
            Route::middleware('web')
                ->group(base_path('routes/master/branch.php'));
            Route::middleware('web')
                ->group(base_path('routes/master/consignee.php'));
            Route::middleware('web')
                ->group(base_path('routes/master/warehouse.php'));
            Route::middleware('web')
                ->group(base_path('routes/master/employee.php'));
            Route::middleware('web')
                ->group(base_path('routes/master/employee_position.php'));
            Route::middleware('web')
                ->group(base_path('routes/master/division.php'));
            Route::middleware('web')
                ->group(base_path('routes/master/distributor.php'));
            Route::middleware('web')
                ->group(base_path('routes/shipping/shipping_instruction.php'));
            Route::middleware('web')
                ->group(base_path('routes/shipping/delivery_order.php'));
            Route::middleware('web')
                ->group(base_path('routes/admin/script.php'));
            Route::middleware('web')
                ->group(base_path('routes/trx_warehouse/trx_warehouse.php'));
            Route::middleware('web')
                ->group(base_path('routes/shipping/container_history.php'));
        });
    }
}
