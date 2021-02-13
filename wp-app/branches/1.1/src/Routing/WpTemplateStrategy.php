<?php

declare(strict_types=1);

namespace Pollen\WpApp\Routing\Strategy;

use Pollen\Http\Response;
use Pollen\Routing\Strategy\ApplicationStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use WP_Query;

class WpTemplateStrategy extends ApplicationStrategy
{
    /**
     * Indicateur de désactivation de la requête WpQuery.
     * @var bool
     */
    protected $wpQueryDisabled = false;

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
     * @inheritDoc
     */
    public function invokeRouteCallable(Route $route, PsrRequest $request): PsrResponse
    {
        if ($this->wpQueryDisabled) {
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

        return $this->decorateResponse(Response::createPsr()->withStatus(100));
    }

    /**
     * Désactivation de la requête WpQuery.
     *
     * @return static
     */
    public function disableWpQuery(): self
    {
        $this->wpQueryDisabled = true;

        return $this;
    }
}
