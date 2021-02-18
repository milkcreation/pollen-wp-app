<?php

declare(strict_types=1);

namespace Pollen\WpApp\Routing;

use InvalidArgumentException;
use Pollen\Cookie\CookieJarInterface;
use Pollen\Http\Request;
use Pollen\WpApp\WpAppInterface;
use Pollen\Routing\RouterInterface;

class Routing
{
    /**
     * @var RouterInterface;
     */
    protected $router;

    /**
     * @var WpAppInterface;
     */
    protected $app;

    /**
     * @param RouterInterface $router
     * @param WpAppInterface $app
     */
    public function __construct(RouterInterface $router, WpAppInterface $app)
    {
        $this->router = $router;
        $this->app = $app;

        if ($fallback = $this->app->config('router.fallback')) {
            $this->router->setFallback($fallback);
        }

        if ($this->app->has(CookieJarInterface::class)) {
            $this->router->middle('queued-cookies');
        }

        add_action(
            'wp',
            function () {
                $request = Request::getFromGlobals();
                $response = $this->router->handleRequest(Request::getFromGlobals());

                add_action('template_redirect', function () use ($request, $response) {
                    $this->router->sendResponse($response);
                    $this->router->terminateEvent($request, $response);
                });

                /* * /
                if (wp_using_themes() && $request->isMethod('GET')) {
                    if (config('routing.remove_trailing_slash', true)) {
                        $permalinks = get_option('permalink_structure');
                        if (substr($permalinks, -1) == '/') {
                            update_option('permalink_structure', rtrim($permalinks, '/'));
                        }

                        $path = Request::getBaseUrl() . Request::getPathInfo();

                        if (($path != '/') && (substr($path, -1) == '/')) {
                            $dispatcher = new Dispatcher($this->manager->getData());
                            $match = $dispatcher->dispatch($method, rtrim($path, '/'));

                            if ($match[0] === FastRoute::FOUND) {
                                $redirect_url = rtrim($path, '/');
                                $redirect_url .= ($qs = Request::getQueryString()) ? "?{$qs}" : '';

                                $response = HttpRedirect::createPsr($redirect_url);
                                $this->manager->emit($response);
                                exit;
                            }
                        }
                    }
                }
                /**/
            },
            999999
        );
    }
}