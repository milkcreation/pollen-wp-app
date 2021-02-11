<?php

declare(strict_types=1);

namespace Pollen\WpApp\Routing;

use Pollen\WpApp\Routing\Concerns\StrategyAwareTrait;
use Pollen\WpApp\Support\Concerns\ContainerAwareTrait;
use League\Route\RouteGroup as BaseRouteGroup;

class RouteGroup extends BaseRouteGroup
{
    use ContainerAwareTrait;
    use StrategyAwareTrait;
}