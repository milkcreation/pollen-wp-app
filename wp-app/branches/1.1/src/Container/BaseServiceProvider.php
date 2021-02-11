<?php

declare(strict_types=1);

namespace Pollen\WpApp\Container;

use League\Container\ServiceProvider\AbstractServiceProvider;

class BaseServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [];

    /**
     * @inheritDoc
     */
    public function boot(): void {}

    /**
     * @inheritDoc
     */
    public function register(): void {}
}