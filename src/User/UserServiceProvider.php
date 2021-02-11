<?php

declare(strict_types=1);

namespace Pollen\WpApp\User;

use Pollen\WpApp\Container\BaseServiceProvider;

class UserServiceProvider extends BaseServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        UserRoleManagerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_action(
            'init',
            function () {
                global $wp_roles;

                /** @var UserRoleManagerInterface $manager */
                $manager = $this->getContainer()->get(UserRoleManagerInterface::class);

                foreach ($wp_roles->roles as $role => $data) {
                    if (!$manager->get($role)) {
                        $manager->register(
                            $role,
                            [
                                'display_name' => translate_user_role($data['name']),
                                'capabilities' => array_keys($data['capabilities']),
                            ]
                        );
                    }
                }
            },
            999998
        );
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            UserRoleManagerInterface::class,
            function () {
                return new UserRoleManager($this->getContainer());
            }
        );
    }
}
