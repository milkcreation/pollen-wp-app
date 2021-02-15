<?php

use Pollen\WpApp\WpApp;
use Pollen\WpApp\WpAppInterface;
use Pollen\Partial\PartialDriverInterface;

if (!function_exists('app')) {
    function app(): WpAppInterface
    {
        return WpApp::instance();
    }
}

if (!function_exists('partial')) {
    /**
     * Instance de portions d'affichage.
     *
     * @param string|null $alias Alias de qualification.
     * @param mixed $idOrParams Identifiant de qualification|Liste des attributs de configuration.
     * @param array $params Liste des attributs de configuration.
     *
     * @return PartialDriverInterface|null
     */
    function partial(string $alias, $idOrParams = null, array $params = []): ?PartialDriverInterface
    {
        return app()->partial($alias, $idOrParams, $params);
    }
}