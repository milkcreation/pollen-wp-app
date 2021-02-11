<?php

declare(strict_types=1);

namespace Pollen\WpApp\Routing\Strategy;

use Pollen\WpApp\Http\Response;
use Pollen\WpApp\Http\ResponseInterface;
use League\Route\Strategy\JsonStrategy as BaseJsonStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;

class JsonStrategy extends BaseJsonStrategy
{
    /**
     * @inheritdoc
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
            $response = $this->isJsonSerializable($response)
                ? (new Response(json_encode($response)))->psr() : (new Response())->psr();
        }
        return $this->decorateResponse($response);
    }
}