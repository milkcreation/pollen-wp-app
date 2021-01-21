<?php

declare(strict_types=1);

namespace Pollen\WpApp\View;

/**
 * @mixin \League\Plates\Engine
 */
interface ViewEngineInterface
{
    /**
     * @param string $name
     *
     * @return ViewTemplate
     */
    public function make(string $name): ViewTemplate;

    /**
     * Définition d'une variable partagée passée à l'ensemble des gabarits
     *
     * @param string $key Clé d'indice de la variable.
     * @param mixed $value Valeur de la variable.
     *
     * @return static
     */
    public function share(string $key, $value = null): ViewEngineInterface;
}