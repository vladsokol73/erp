<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerBladeExtensions();
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    public function registerBladeExtensions() {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();


        $blade->directive('role', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->hasRole({$expression})): ?>";
        });

        $blade->directive('endrole', function () {
            return '<?php endif; ?>';
        });


        $blade->directive('permission', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->hasPermissionTo({$expression})): ?>";
        });

        $blade->directive('endpermission', function () {
            return '<?php endif; ?>';
        });
    }
}
