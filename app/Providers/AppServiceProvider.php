<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
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
        Schema::defaultStringLength(191);

        Validator::replacer('after_or_equal', function ($message, $attribute, $rule, $parameters) {
            // Formata a data para "d/m/Y"
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $parameters[0])->format('d/m/Y');
            return str_replace(':date', $date, $message);
        });
    }
}
