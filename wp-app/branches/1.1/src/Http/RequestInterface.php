<?php

declare(strict_types=1);

namespace Pollen\WpApp\Http;

use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

/**
 * @mixin BaseRequest
 */
interface RequestInterface
{
    /**
     * Creation d'une instance depuis une instance de requête symfony.
     *
     * @param BaseRequest $request
     *
     * @return static
     */
    public static function createFromBase(BaseRequest $request): RequestInterface;

    /**
     * Création d'une instance depuis une requête PSR-7.
     *
     * @param PsrRequest $psrRequest
     *
     * @return static
     */
    public static function createFromPsr(PsrRequest $psrRequest): RequestInterface;

    /**
     * Convertion d'une instance de requête en requête HTTP Psr-7
     *
     * @param BaseRequest|null $request
     *
     * @return PsrRequest|null
     */
    public static function createPsr(?BaseRequest $request = null): ?PsrRequest;

    /**
     * Récupération de l'instance basée sur les variables globales.
     *
     * @return static
     */
    public static function getFromGlobals(): RequestInterface;

    /**
     * Récupération du prefixe d'url
     *
     * @return static
     */
    public function getRewriteBase(): string;

    /**
     * Conversion au format PSR-7.
     *
     * @return ResponseInterface|null
     */
    public function psr(): ?PsrRequest;
}