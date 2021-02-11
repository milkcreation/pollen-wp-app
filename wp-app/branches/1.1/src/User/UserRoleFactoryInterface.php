<?php

declare(strict_types=1);

namespace Pollen\WpApp\User;

interface UserRoleFactoryInterface
{
    /**
     * Récupération de la liste des habilitations.
     *
     * @return array
     */
    public function getCapabilities(): array;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Définition d'une habilitation.
     *
     * @param string $cap
     *
     * @return static
     */
    public function setCapability(string $cap): UserRoleFactoryInterface;

    /**
     * Définition d'une liste d'habilitation.
     *
     * @param string[] $capabilities
     *
     * @return static
     */
    public function setCapabilities(array $capabilities): UserRoleFactoryInterface;

    /**
     * Définition de l'intitulé de qualification.
     *
     * @param string $displayName
     *
     * @return static
     */
    public function setDisplayName(string $displayName): UserRoleFactoryInterface;
}