<?php namespace Bishopm\Hgrh\Providers;

use Bishopm\Hgrh\Http\Middleware\AdminRoute;
use Bishopm\Hgrh\Livewire\FileBrowser;
use Bishopm\Hgrh\Livewire\Search;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

class HgrhServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('adminonly', AdminRoute::class);
        Schema::defaultStringLength(191);
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'hgrh');
        Paginator::useBootstrapFive();
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        Livewire::component('file-browser', FileBrowser::class);
        Livewire::component('search', Search::class);
        Blade::componentNamespace('Bishopm\\Hgrh\\Resources\\Views\\Components', 'hgrh');
        Config::set('auth.providers.users.model','Bishopm\Hgrh\Models\User');
        if (env('APP_ENV')=="local"){
            $this->publishes([
                __DIR__.'/../Resources/pwa/local_manifest.json' => public_path('manifest.json'),
                __DIR__.'/../Resources/pwa/local_serviceworker.js' => public_path('serviceworker.js'),
            ]);
        } else {
            $this->publishes([
                __DIR__.'/../Resources/pwa/manifest.json' => public_path('manifest.json'),
                __DIR__.'/../Resources/pwa/serviceworker.js' => public_path('serviceworker.js'),
            ]);
        }
        Relation::morphMap([
            'document' => 'Bishopm\Hgrh\Models\Document'
        ]);
    }

    /**
     * Register any hgrh services.
     *
     * @return void
     */
    public function register(): void
    {
        foreach (glob(__DIR__ . '/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hgrh'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../../config/hgrh.php' => config_path('hgrh.php'),
        ], 'hgrh.config');

        // Publishing the views.
        // $this->publishes([
        //    __DIR__.'/../Resources' => public_path('vendor/bishopm'),
        // ], 'hgrh.views');

        // Publishes assets.
        $this->publishes([
            __DIR__.'/../Resources/assets' => public_path('hgrh'),
          ], 'assets');
        

        // Registering hgrh commands.
        $this->commands([
            'Bishopm\Hgrh\Console\Commands\InstallHgrh'
        ]);
    }
}
