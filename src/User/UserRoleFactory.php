<?php

declare(strict_types=1);

namespace Pollen\WpApp\User;

use InvalidArgumentException;
use Throwable;
use WP_Role;

class UserRoleFactory implements UserRoleFactoryInterface
{
    /**
     * Liste des habilitations.
     * @var array
     */
    protected $capabilities = [];

    /**
     * Intitulé de qualification.
     * @var string
     */
    protected $displayName = '';

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * @param string $name
     * @param array $args
     */
    public function __construct(string $name, array $args = [])
    {
        $this->name = $name;

        if (isset($args['capabilities'])) {
            try {
                $this->setCapabilities($args['capabilities']);
            } catch (Throwable $e) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid capabilities declaration for UserRole [%s]',
                        $this->name
                    )
                );
            }
        }

        if (isset($args['display_name'])) {
            try {
                $this->setDisplayName($args['display_name']);
            } catch (Throwable $e) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid display name declaration for UserRole [%s]',
                        $this->name
                    )
                );
            }
        } else {
            $this->setDisplayName($this->name);
        }

        global $wp_roles;

        /** @var WP_Role $role */
        if (!$role = $wp_roles->get_role($this->name)) {
            $role = $wp_roles->add_role($this->name, $this->getDisplayName());
        } elseif (($names = $wp_roles->get_names()) && ($names[$this->name] !== $this->getDisplayName())) {
            $wp_roles->remove_role($this->name);
            $role = $wp_roles->add_role($this->name, $this->getDisplayName());
        }

        foreach ($this->getCapabilities() as $cap) {
            if (!isset($role->capabilities[$cap]) || ($role->capabilities[$cap] !== true)) {
                $role->add_cap($cap, true);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getCapabilities(): array
    {
        return $this->capabilities;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setCapability(string $cap): UserRoleFactoryInterface
    {
        if (!isset($this->capabilities[$cap])) {
            $this->capabilities[] = $cap;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCapabilities(array $capabilities): UserRoleFactoryInterface
    {
        foreach ($capabilities as $cap) {
            $this->setCapability($cap);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDisplayName(string $displayName): UserRoleFactoryInterface
    {
       $this->displayName = $displayName;

       return $this;
    }
}