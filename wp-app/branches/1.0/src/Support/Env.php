<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support;

use Illuminate\Support\Env as BaseEnv;

class Env extends BaseEnv
{
    /**
     * Vérifie si l'environnement d'éxecution est en développement.
     *
     * @return bool
     */
    public static function isDev(): bool
    {
        return static::get('APP_ENV') === 'dev' || static::get('APP_ENV') === 'developpement';
    }

    /**
     * Vérifie si l'environnement d'éxecution est en production.
     *
     * @return bool
     */
    public static function isProd()
    {
        return static::get('APP_ENV') === 'prod' || static::get('APP_ENV') === 'production';
    }

    /**
     * Vérifie si l'environnement d'éxecution est en recette.
     *
     * @return bool
     */
    public static function isStaging()
    {
        return static::get('APP_ENV') === 'staging';
    }
}