<?php

declare(strict_types=1);

namespace Pollen\WpApp\Validation;

use Pollen\WpApp\Container\ServiceProvider;
use Pollen\WpApp\Validation\Rules\PasswordRule;
use Pollen\WpApp\Validation\Rules\SerializedRule;

class ValidationServiceProvider extends ServiceProvider
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
    public function boot(): void
    {
        /** @var ValidatorInterface $validator */
        $validator = $this->getContainer()->get(ValidatorInterface::class);
        $rules = [
            'password'   => new PasswordRule(),
            'serialized' => new SerializedRule(),
        ];
        foreach ($rules as $name => $rule) {
            $validator->setCustomRule($name, $rule);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(ValidatorInterface::class, function () {
            return new Validator();
        });
    }
}