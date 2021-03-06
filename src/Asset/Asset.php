<?php

declare(strict_types=1);

namespace Pollen\WpApp\Asset;

use Pollen\Asset\AssetManagerInterface;
use Pollen\WpApp\WpAppInterface;

class Asset
{
    /**
     * @var WpAppInterface
     */
    protected $app;

    /**
     * @var AssetManagerInterface
     */
    protected $asset;

    /**
     * @param AssetManagerInterface $asset
     * @param WpAppInterface $app
     */
    public function __construct(AssetManagerInterface $asset, WpAppInterface $app)
    {
        $this->asset = $asset;
        $this->app = $app;

        add_action('wp_head', function () {
            echo $this->asset->headerStyles();
            echo $this->asset->headerScripts();
        }, 5);

        add_action('wp_footer', function () {
            echo $this->asset->footerScripts();
        }, 5);
    }
}
