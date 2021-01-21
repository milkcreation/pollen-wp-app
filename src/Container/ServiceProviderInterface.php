<?php

declare(strict_types=1);

namespace Pollen\WpApp\Container;

use League\Container\ServiceProvider\ServiceProviderInterface as BaseBaseServiceProviderInterface;

interface ServiceProviderInterface extends BaseBaseServiceProviderInterface
{
    /**
     * Initialisation du fournisseur de service.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Déclaration de services.
     *
     * @return void
     */
    public function register(): void;
}