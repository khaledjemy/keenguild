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
        // The homepage video field allows up to 150 MB; Livewire's default temporary
        // upload cap is 12 MB and would reject the file before field validation.
        config()->set('livewire.temporary_file_upload.rules', ['required', 'file', 'max:153600']);
    }
}
