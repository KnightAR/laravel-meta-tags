<?php

namespace Torann\LaravelMetaTags;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class MetaTagsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('meta-tags.php')
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // $this->app['metatag'] = $this->app->share(function ($app) { // Removed to support laravel 5.4
       $this->app->singleton('metatag', function ($app) {
            return new MetaTag(
                $app['request'],
                $app['config']['meta-tags'],
                $app['config']->get('app.locale')
            );
        });

        $this->addBladeDirectives();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['metatag'];
    }

    /**
     * Register blade directives
     *
     * @return void
     */
    protected function addBladeDirectives()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('meta', function ($arguments) {
                return "<?php echo MetaTag::tag($arguments); ?>";
            });

            $bladeCompiler->directive('metas', function ($arguments) {
                return "<?php echo MetaTag::tags($arguments); ?>";
            });

            $bladeCompiler->directive('metaopengraph', function () {
                return "<?php echo MetaTag::openGraph(); ?>";
            });
        });
    }
}
