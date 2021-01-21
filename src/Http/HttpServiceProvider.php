<?php

declare(strict_types=1);

namespace Pollen\WpApp\Http;

use Pollen\WpApp\Container\ServiceProvider;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        PsrRequestInterface::class,
        RequestInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(RequestInterface::class, function () {
            return Request::getFromGlobals();
        });

        $this->getContainer()->share(PsrRequestInterface::class, function () {
            return Request::createPsr();
        });
    }
}
