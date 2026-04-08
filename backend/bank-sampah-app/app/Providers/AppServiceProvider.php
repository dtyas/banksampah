<?php

namespace App\Providers;

use App\Repositories\Contracts\KategoriSampahRepositoryInterface;
use App\Repositories\Contracts\NasabahRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PembayaranRepositoryInterface;
use App\Repositories\Contracts\SampahRepositoryInterface;
use App\Repositories\Contracts\TransaksiRepositoryInterface;
use App\Repositories\Eloquent\KategoriSampahRepository;
use App\Repositories\Eloquent\NasabahRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\PembayaranRepository;
use App\Repositories\Eloquent\SampahRepository;
use App\Repositories\Eloquent\TransaksiRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NasabahRepositoryInterface::class, NasabahRepository::class);
        $this->app->bind(KategoriSampahRepositoryInterface::class, KategoriSampahRepository::class);
        $this->app->bind(SampahRepositoryInterface::class, SampahRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(TransaksiRepositoryInterface::class, TransaksiRepository::class);
        $this->app->bind(PembayaranRepositoryInterface::class, PembayaranRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability): ?bool {
            return $user->role === 'super_admin' ? true : null;
        });
    }
}
