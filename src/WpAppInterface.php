<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Pollen\WpApp\Container\Container;
use Pollen\WpApp\Http\RequestInterface;
use Pollen\WpApp\Routing\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;

/**
 * @mixin \Pollen\WpApp\Support\Concerns\BootableTrait
 * @mixin \Pollen\WpApp\Support\Concerns\ConfigBagTrait
 * @mixin \Pollen\WpApp\Support\Concerns\ContainerAwareTrait
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
     * Instance du conteneur d'injection de dépendances.
     *
     * @return ContainerInterface|Container|null
     */
    public function getContainer(): ?ContainerInterface;

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
}