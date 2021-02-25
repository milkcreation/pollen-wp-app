<?php

use Pollen\Field\FieldDriverInterface;
use Pollen\Partial\PartialDriverInterface;
use Pollen\WpApp\WpApp;
use Pollen\WpApp\WpAppInterface;

if (!function_exists('app')) {
    function app(): WpAppInterface
    {
        return WpApp::instance();
    }
}

if (!function_exists('field')) {
    /**
     * Instance de champ.
     *
     * @param string|null $alias Alias de qualification.
     * @param mixed $idOrParams Identifiant de qualification|Liste des attributs de configuration.
     * @param array $params Liste des attributs de configuration.
     *
     * @return FieldDriverInterface|null
     */
    function field(string $alias, $idOrParams = null, array $params = []): ?FieldDriverInterface
    {
        return app()->field($alias, $idOrParams, $params);
    }
}

if (!function_exists('partial')) {
    /**
     * Instance de portion d'affichage.
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