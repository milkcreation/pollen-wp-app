<?php

declare(strict_types=1);

namespace Pollen\WpApp\User;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface UserRoleManagerInterface extends ContainerProxyInterface
{
    /**
     * Récupération d'une instance de rôle déclaré.
     *
     * @param string $name.
     *
     * @return UserRoleFactoryInterface
     */
    public function get(string $name): ?UserRoleFactoryInterface;

    /**
     * Déclaration d'un rôle.
     *
     * @param string $name
     * @param UserRoleFactoryInterface|array $args
     *
     * @return static
     */
    public function register(string $name, $args): UserRoleManagerInterface;
}