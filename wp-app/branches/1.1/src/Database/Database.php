<?php

declare(strict_types=1);

namespace Pollen\WpApp\Database;

use Pollen\Database\DatabaseManagerInterface;
use Pollen\WpApp\WpAppInterface;

class Database
{
    /**
     * @var DatabaseManagerInterface
     */
    protected $dbManager;

    /**
     * @var WpAppInterface
     */
    protected $app;

    /**
     * @param DatabaseManagerInterface $dbManager
     * @param WpAppInterface $app
     */
    public function __construct(DatabaseManagerInterface $dbManager, WpAppInterface $app)
    {
        $this->dbManager = $dbManager;
        $this->app = $app;

        global $wpdb;

        $this->dbManager->addConnection(
            [
                'driver'    => 'mysql',
                'host'      => DB_HOST,
                'database'  => DB_NAME,
                'username'  => DB_USER,
                'password'  => DB_PASSWORD,
                'charset'   => DB_CHARSET,
                'collation' => DB_COLLATE,
                'prefix'    => is_multisite() ? $wpdb->prefix : $wpdb->base_prefix,
            ]
        );
        $this->dbManager->setAsGlobal();
        $this->dbManager->bootEloquent();

        if (is_multisite()) {
            $this->dbManager->addConnection(
                [
                    'driver'    => 'mysql',
                    'host'      => DB_HOST,
                    'database'  => DB_NAME,
                    'username'  => DB_USER,
                    'password'  => DB_PASSWORD,
                    'charset'   => DB_CHARSET,
                    'collation' => DB_COLLATE,
                    'prefix'    => $wpdb->base_prefix,
                ],
                'wp.user'
            );
        }
    }
}