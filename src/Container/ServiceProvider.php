<?php

declare(strict_types=1);

namespace Pollen\WpApp\Container;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [];

    /**
     * Initialisation du fournisseur de service.
     *
     * @return void
     */
    public function boot(): void {}

    /**
     * Déclaration de services.
     *
     * @return void
     */
    public function register(): void {}
}