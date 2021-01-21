<?php

declare(strict_types=1);

namespace Pollen\WpApp\Routing;

use Pollen\WpApp\Routing\Concerns\StrategyAwareTrait;
use Pollen\WpApp\Support\Concerns\ContainerAwareTrait;
use League\Route\Route as BaseRoute;

class Route extends BaseRoute
{
    use ContainerAwareTrait;
    use StrategyAwareTrait;
}