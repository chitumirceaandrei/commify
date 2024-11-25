<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TaxCalculatorInterface;
use App\Services\ProgressiveTaxCalculator;
use App\Repositories\TaxBandRepositoryInterface;
use App\Repositories\TaxBandRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
         // Bind the TaxCalculatorInterface to ProgressiveTaxCalculator
        $this->app->singleton(TaxCalculatorInterface::class, ProgressiveTaxCalculator::class);

        // Bind the TaxBandRepositoryInterface to TaxBandRepository
        $this->app->singleton(TaxBandRepositoryInterface::class, TaxBandRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        //
    }
}
