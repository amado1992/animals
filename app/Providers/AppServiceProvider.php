<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        Relation::morphMap([
            'offer'            => 'App\Models\Offer',
            'order'            => 'App\Models\Order',
            'wanted'           => 'App\Models\Wanted',
            'general_document' => 'App\Models\GeneralDocument',
            'email'            => 'App\Models\Email',
            'attachment'       => 'App\Models\Attachment',
        ]);
    }
}
