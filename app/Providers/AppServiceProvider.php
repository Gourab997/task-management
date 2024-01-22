<?php

namespace App\Providers;

use App\Repositories\BatchTemplateRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\Interfaces\BatchTemplateRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Repositories\Interfaces\OutgoingBatchRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ShipmentRepositoryInterface;
use App\Repositories\Interfaces\StockRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\OrganizationRepository;
use App\Repositories\OutgoingBatchRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShipmentRepository;
use App\Repositories\StockRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
        $this->app->bind(BatchTemplateRepositoryInterface::class, BatchTemplateRepository::class);
        $this->app->bind(OutgoingBatchRepositoryInterface::class, OutgoingBatchRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(ShipmentRepositoryInterface::class, ShipmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data = null, $message = null, $code = 200) {
            return Response::json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $code);
        });

        Response::macro('error', function ($message = null, $code = 400) {
            return Response::json([
                'success' => false,
                'message' => $message,
            ], $code);
        });
    }
}
