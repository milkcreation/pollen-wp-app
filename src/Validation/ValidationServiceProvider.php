<?php

declare(strict_types=1);

namespace Pollen\WpApp\Validation;

use Pollen\WpApp\Container\BaseServiceProvider;

class ValidationServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        ValidatorInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(ValidatorInterface::class, function () {
            return new Validator();
        })->addTag('validator');
    }
}