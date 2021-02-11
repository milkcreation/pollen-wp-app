<?php

declare(strict_types=1);

namespace Pollen\WpApp\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

trait ResponseTrait
{
    /**
     * Creation d'une instance depuis une instance de réponse symfony.
     *
     * @param BaseResponse|ResponseInterface $response
     *
     * @return static
     */
    public static function createFromBase(BaseResponse $response): self
    {
        return new static($response->getContent(), $response->getStatusCode(), $response->headers->all());
    }

    /**
     * Création d'une instance depuis une réponse PSR-7.
     *
     * @param PsrResponse $psrResponse
     * @param boolean $streamed
     *
     * @return static
     */
    public static function createFromPsr(PsrResponse $psrResponse, bool $streamed = false): self
    {
        return static::createFromBase((new HttpFoundationFactory())->createResponse($psrResponse, $streamed));
    }

    /**
     * Convertion d'une instance de réponse en réponse HTTP PSR-7.
     *
     * @param BaseResponse|ResponseInterface|null $response
     *
     * @return static|null
     */
    public static function createPsr($response = null): ?PsrResponse
    {
        if ($response = $response ?: new static()) {
            $psr17Factory = new Psr17Factory();
            $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

            return $psrHttpFactory->createResponse($response);
        }
        return null;
    }

    /**
     * Conversion au format PSR-7.
     *
     * @return static|null
     */
    public function psr(): ?PsrResponse
    {
        return self::createPsr($this);
    }
}