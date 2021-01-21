<?php

declare(strict_types=1);

namespace Pollen\WpApp\Routing\Strategy;

use Pollen\WpApp\Http\Response;
use Pollen\WpApp\Http\ResponseInterface;
use League\Route\Strategy\ApplicationStrategy as BaseApplicationStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;

class ApplicationStrategy extends BaseApplicationStrategy
{
    /**
     * @inheritDoc
     */
    public function invokeRouteCallable(Route $route, PsrRequest $request): PsrResponse
    {
        $controller = $route->getCallable($this->getContainer());

        $args = array_values($route->getVars());
        array_push($args, $request);
        $response = $controller(...$args);

        if ($response instanceof ResponseInterface) {
            $response = $response->psr();
        } elseif (!$response instanceof PsrResponse) {
            $response = is_string($response) ? (new Response($response))->psr() : (new Response())->psr();
        }
        return $this->applyDefaultResponseHeaders($response);
    }
}
