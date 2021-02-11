<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Pollen\WpApp\Http\RequestInterface;
use Pollen\WpApp\Routing\RouterInterface;
use Pollen\WpApp\Validation\ValidatorInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;

/**
 * @mixin \Pollen\WpApp\Container\Container
 * @mixin \Pollen\WpApp\Support\Concerns\BootableTrait
 * @mixin \Pollen\WpApp\Support\Concerns\ConfigBagTrait
 */
interface WpAppInterface
{
    /**
     * Récupération de l'instance.
     *
     * @return static
     */
    public static function instance(): WpAppInterface;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): WpAppInterface;

    /**
     * Instance de la requête HTTP globale au format PSR-7.
     *
     * @return PsrRequest|null
     */
    public function psrRequest(): ?PsrRequest;

    /**
     * Instance de la requête HTTP globale.
     *
     * @return RequestInterface|null
     */
    public function request(): ?RequestInterface;

    /**
     * Instance du gestionnaire de routage.
     *
     * @return RouterInterface|null
     */
    public function router(): ?RouterInterface;

    /**
     * Instance du gestionnaire de validation.
     *
     * @return ValidatorInterface|null
     */
    public function validator(): ?ValidatorInterface;
}