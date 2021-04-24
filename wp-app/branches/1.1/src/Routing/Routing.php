<?php

declare(strict_types=1);

namespace Pollen\WpApp\Routing;

use Pollen\Support\Proxy\HttpRequestProxy;
use Pollen\WpApp\Middleware\WpAdminMiddleware;
use Pollen\WpApp\WpAppInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Routing\UrlMatcher;
use Pollen\WpHook\WpHookerProxy;
use WP_Query;

class Routing
{
    use HttpRequestProxy;
    use WpHookerProxy;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var WpAppInterface
     */
    protected $app;

    /**
     * Contextes d'affichage de gabarits Wordpress.
     * @var string[]
     */
    protected $wpQueryTag = [
        'is_single',
        'is_preview',
        'is_page',
        'is_archive',
        'is_date',
        'is_year',
        'is_month',
        'is_day',
        'is_time',
        'is_author',
        'is_category',
        'is_tag',
        'is_tax',
        'is_search',
        'is_feed',
        'is_comment_feed',
        'is_trackback',
        'is_home',
        'is_404',
        'is_embed',
        'is_paged',
        'is_admin',
        'is_attachment',
        'is_singular',
        'is_robots',
        'is_posts_page',
        'is_post_type_archive',
    ];

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

        if (is_admin()) {
            add_action(
                'admin_init',
                function () {
                    $request = $this->httpRequest();
                    $urlMatcher = new UrlMatcher($this->router, $request);
                    $urlMatcher->match();
                }
            );
        } else {
            add_action(
                'parse_request',
                function () {
                    $request = $this->httpRequest();
                    $urlMatcher = new UrlMatcher($this->router, $request);
                    $urlMatcher->match();

                    if ($request->attributes->has('_route')) {
                        $route = $request->attributes->get('_route');

                        $this->router->setCurrentRoute($route);

                        if ($hook = $this->wpHooker()->getRouteHookable($route)) {
                            add_action(
                                'pre_get_posts',
                                function (WP_Query $wp_query) use ($hook) {
                                    if (!$wp_query->is_admin && $wp_query->is_main_query()) {
                                        $wp_query->set('page_id', $hook->getId());
                                    }
                                },
                                0
                            );
                        } else {
                            add_action(
                                'pre_get_posts',
                                function (WP_Query $wp_query) {
                                    if (!$wp_query->is_admin && $wp_query->is_main_query()) {
                                        foreach ($this->wpQueryTag as $ct) {
                                            $wp_query->{$ct} = false;
                                        }
                                        $wp_query->query_vars = $wp_query->fill_query_vars([]);
                                        unset($wp_query->query);
                                    }
                                },
                                0
                            );

                            add_action(
                                'wp',
                                function () {
                                    global $wp_query;

                                    if (!$wp_query->is_admin && $wp_query->is_main_query()) {
                                        $wp_query->is_404 = false;
                                        $wp_query->query = [];
                                        status_header(200);
                                    }
                                }
                            );

                            add_filter(
                                'posts_pre_query',
                                function (?array $posts, WP_Query $wp_query) {
                                    if (!$wp_query->is_admin && $wp_query->is_main_query()) {
                                        return [];
                                    }
                                    return $posts;
                                },
                                10,
                                2
                            );
                        }
                    }
                },
                0
            );
        }

        add_action(
            'template_redirect',
            function () {
                $request = $this->router->getHandleRequest();
                $response = $this->router->handleRequest();

                $this->router->sendResponse($response);
                $this->router->terminateEvent($request, $response);

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