<?php

namespace App\Providers;

use App\Models\Profile;
use App\Models\SocialLink;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // Data every public page needs (header/footer).
        View::composer(['home', 'projects.show'], function ($view) {
            try {
                $view->with('siteProfile', Profile::current())
                    ->with('socialLinks', SocialLink::active()->ordered()->get());
            } catch (\Throwable $e) {
                $view->with('siteProfile', new Profile())->with('socialLinks', collect());
            }
        });
    }
}
