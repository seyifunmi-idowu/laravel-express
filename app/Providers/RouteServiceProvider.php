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

    public function boot()
    {
        $this->configureRateLimiting();

    }

    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        // $this->mapApiRoute('/api/auth.php', '/api/v1/auth');
        // $this->mapApiRoute('/api/bank.php', '/api/v1/bank');
        // $this->mapApiRoute('/api/card.php', '/api/v1/card');
        $this->mapApiRoute('/api/customer.php', '/api/v1/customer');
        // $this->mapApiRoute('/api/maps.php', '/api/v1/maps');
        // $this->mapApiRoute('/api/notification.php', '/api/v1/notification');
        // $this->mapApiRoute('/api/order.php', '/api/v1/order');
        // $this->mapApiRoute('/api/paystack.php', '/api/v1/paystack');
        // $this->mapApiRoute('/api/rider.php', '/api/v1/rider');
        // $this->mapApiRoute('/api/user.php', '/api/v1/user');
        $this->mapApiRoute('/api/vehicle.php', '/api/v1/vehicles');
        // $this->mapApiRoute('/api/wallet.php', '/api/v1/wallet');

        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapApiRoute(string $file, string $prefix = '')
    {
        Route::prefix($prefix)
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/' . $file));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
