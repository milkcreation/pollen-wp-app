<?php

use Pollen\WpApp\WpApp;
use Pollen\Partial\PartialDriverInterface;

if (!function_exists('app')) {
    function app(): WpApp
    {
        return WpApp::instance();
    }
}

if (!function_exists('partial')) {
    function partial(string $alias): PartialDriverInterface
    {
        return app()->partial($alias);
    }
}