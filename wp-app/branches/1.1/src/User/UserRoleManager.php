<?php

declare(strict_types=1);

namespace Pollen\WpApp\User;

use Pollen\WpApp\Support\Concerns\ContainerAwareTrait;
use Psr\Container\ContainerInterface as Container;

class UserRoleManager implements UserRoleManagerInterface
{
    use ContainerAwareTrait;

    /**
     * Liste des roles déclarés.
     * @var UserRoleFactoryInterface[]|array
     */
    public $roles = [];

    /**
     * @param Container|null $container
     */
    public function __construct(?Container $container = null)
    {
        if ($container !== null) {
            $this->setContainer($container);
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ?UserRoleFactoryInterface
    {
        return $this->roles[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function register(string $name, $args): UserRoleManagerInterface
    {
        if (!$args instanceof UserRoleFactoryInterface) {
            $role = new UserRoleFactory($name, is_array($args) ? $args : []);
        } else {
            $role = $args;
        }
        $this->roles[$name] = $role;

        return $this;
    }
}