<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support\Concerns;

use Psr\Container\ContainerInterface as Container;

trait ContainerAwareTrait
{
    /**
     * Instance du conteneur d'injection de dépendance.
     * @var Container|null
     */
    private $container;

    /**
     * Vérification de disponibilité d'un service fourni par le conteneur d'injection de dépendances.
     *
     * @param string $alias Alias de qualification du service.
     *
     * @return bool
     */
    public function containerHas(string $alias): bool
    {
        return $this->getContainer() && $this->getContainer()->has($alias);
    }

    /**
     * Récupération d'un service fourni par le conteneur d'injection de dépendances.
     *
     * @param string $alias Alias de qualification du service.
     *
     * @return mixed|null
     */
    public function containerGet(string $alias)
    {
        return $this->getContainer() ? $this->getContainer()->get($alias) : null;
    }

    /**
     * Récupération de l'instance du conteneur d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }
}