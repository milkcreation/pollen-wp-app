<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support;

use Carbon\Carbon;
use DateTime as BaseDateTime;
use DateTimeZone;
use Exception;
use Pollen\WpApp\Http\Request;

class DateTime extends Carbon
{
    /**
     * Format de date par défaut
     * @var string
     */
    protected static $defaultFormat = 'Y-m-d H:i:s';

    /**
     * Instance du fuseau horaire utilisé par défaut.
     * @var DateTimeZone
     */
    protected static $globalTimeZone;

    /**
     * @param string|null $time
     * @param null|DateTimeZone $tz
     *
     * @throws Exception
     */
    public function __construct($time = null, $tz = null)
    {
        if (is_null($tz)) {
            $tz = static::getGlobalTimeZone();
        }
        parent::__construct($time, $tz);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->format(static::$defaultFormat);
    }

    /**
     * {@inheritDoc}
     *
     * @return BaseDateTime|static|null
     */
    public static function createFromFormat($format, $time, $timezone = null)
    {
        return parent::createFromFormat($format, $time, is_null($timezone) ? static::getGlobalTimeZone() : $timezone);
    }

    /**
     * Récupération du fuseau horaire par défaut.
     *
     * @return DateTimeZone
     */
    public static function getGlobalTimeZone(): DateTimeZone
    {
        return static::$globalTimeZone ?: static::setGlobalTimeZone();
    }

    /**
     * Définition du format d'affichage par défault de la date.
     *
     * @param string $format
     *
     * @return string
     */
    public static function setDefaultFormat(string $format): string
    {
        return static::$defaultFormat = $format;
    }

    /**
     * Définition du fuseau horaire par défaut.
     *
     * @param DateTimeZone|null $tz
     *
     * @return DateTimeZone
     */
    public static function setGlobalTimeZone(?DateTimeZone $tz = null): DateTimeZone
    {
        return static::$globalTimeZone = $tz ?: new DateTimeZone(
            Env::get('APP_TIMEZONE') ?: Request::getFromGlobals()->server->get(
                'TZ',
                ini_get('date.timezone') ?: 'UTC'
            )
        );
    }

    /**
     * Récupération de la date locale pour un format donné.
     *
     * @param string|null $format Format d'affichage de la date.
     * @param string|null $locale ex. en|en_GB|fr ...
     *
     * @return string
     */
    public function formatLocale(?string $format = null, ?string $locale = null): string
    {
        if ($locale !== null) {
            $baseLocale = $this->locale ?? null;
            $this->locale($locale);
        }
        $date = $this->settings(['formatFunction' => 'translatedFormat'])->format($format ?: static::$defaultFormat);

        if (isset($baseLocale)) {
            $this->locale($baseLocale);
        }
        return $date;
    }

    /**
     * Récupération de la date basée sur le temps universel pour un format donné.
     *
     * @param string|null $format Format d'affichage de la date. MySQL par défaut.
     *
     * @return string|null
     */
    public function utc(?string $format = null): ?string
    {
        try {
            return (new static(null, 'UTC'))
                ->setTimestamp($this->getTimestamp())->format($format ?: static::$defaultFormat);
        } catch (Exception $e) {
            return null;
        }
    }
}