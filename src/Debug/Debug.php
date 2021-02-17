<?php

declare(strict_types=1);

namespace Pollen\WpApp\Debug;

use Pollen\WpApp\WpAppInterface;
use Pollen\Debug\DebugManagerInterface;

class Debug
{
    /**
     * @var WpAppInterface;
     */
    protected $app;

    /**
     * @var DebugManagerInterface;
     */
    protected $debug;

    /**
     * @param DebugManagerInterface $debug
     * @param WpAppInterface $app
     */
    public function __construct(DebugManagerInterface $debug, WpAppInterface $app)
    {
        $this->debug = $debug;
        $this->app = $app;

        add_action(
            'wp_head',
            function () {
                echo "<!-- DebugBar -->";
                echo $this->debug->debugBar()->renderHead();
                echo "<!-- / DebugBar -->";
            },
            999999
        );

        add_action(
            'wp_footer',
            function () {
                echo $this->debug->debugBar()->render();
            },
            999999
        );
    }
}