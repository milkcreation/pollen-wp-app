<?php

declare(strict_types=1);

namespace Pollen\WpApp\View;

use Pollen\WpApp\Container\BaseServiceProvider;

class ViewServiceProvider extends BaseServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        ViewEngineInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(ViewEngineInterface::class, function () {
            return new ViewEngine(get_template_directory());
        });
    }
}
