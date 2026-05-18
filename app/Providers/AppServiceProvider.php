<?php

namespace App\Providers;

use App\Models\Animal;
use App\Models\Persona;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Animal usa numero_arete como key
        Route::bind('animal', function ($value) {
            return Animal::where('numero_arete', $value)->firstOrFail();
        });

        // Persona usa cedula como key
        Route::bind('persona', function ($value) {
            return Persona::where('cedula', $value)->firstOrFail();
        });
    }
}