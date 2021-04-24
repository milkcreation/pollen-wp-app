<?php

declare(strict_types=1);

namespace Pollen\WpApp\Date;

use Pollen\Support\DateTime;
use Pollen\Support\Env;
use Pollen\WpApp\WpAppInterface;

class Date
{
    /**
     * @var WpAppInterface
     */
    protected $app;

    /**
     * @param WpAppInterface $app
     */
    public function __construct(WpAppInterface $app)
    {
        $this->app = $app;

        $tz = Env::get('APP_TIMEZONE') ?: $this->app->httpRequest()->server->get(
            'TZ',
            ini_get('date.timezone') ?: 'UTC'
        );
        date_default_timezone_set($tz);

        global $locale;
        DateTime::setLocale($locale);
    }
}
