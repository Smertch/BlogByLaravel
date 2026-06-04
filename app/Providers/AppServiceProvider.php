<?php

namespace App\Providers;

use App\Support\SiteUi;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SiteUi::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $siteUi = app(SiteUi::class);
            $request = request();
            $language = $siteUi->language($request);

            $view->with('siteUi', $siteUi);
            $view->with('siteLanguage', $language);
            $view->with('siteLanguages', $siteUi->languages());
            $view->with('ui', fn (string $alias, array $replace = []): string => $siteUi->trans($alias, $replace, $language));
        });
    }
}
