<?php

declare(strict_types=1);

namespace Pollen\WpApp\Cookie;

use Pollen\Cookie\CookieJarInterface;
use Pollen\WpApp\WpAppInterface;
use RuntimeException;
use WP_Site;

class CookieJar
{
    /**
     * @var CookieJarInterface
     */
    protected $cookieJar;

    /**
     * @var WpAppInterface;
     */
    protected $app;

    /**
     * @param CookieJarInterface $cookieJar
     * @param WpAppInterface $app
     */
    public function __construct(CookieJarInterface $cookieJar, WpAppInterface $app)
    {
        $this->cookieJar = $cookieJar;
        $this->app = $app;

        if (is_multisite() && $site = WP_Site::get_instance(get_current_blog_id())) {
            $this->cookieJar->setDefaults($site->path, $site->domain);

            $this->cookieJar->setSalt(
                '_' . md5($site->domain . $site->path . COOKIEHASH)
            );
        } else {
            $this->cookieJar->setSalt('_' . COOKIEHASH);
        }

        try {
            $router = $this->app->router();
            $router->middle('queued-cookies');
        } catch(RuntimeException $e) {
            unset($e);
        }
    }
}