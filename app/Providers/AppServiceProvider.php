<?php

namespace App\Providers;

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
        // Share header categories with all views
        view()->composer('*', function ($view) {
            $headerCategories = \App\Models\Category::where('is_active', true)
                ->withCount('books')
                ->orderBy('name')
                ->limit(10)
                ->get();

            $view->with('headerCategories', $headerCategories);
        });
    }
}
