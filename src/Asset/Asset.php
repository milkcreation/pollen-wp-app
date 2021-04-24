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

        if (!$this->asset->getBaseDir()) {
            $this->asset->setBaseDir(ABSPATH);
        }

        if (!$this->asset->getBaseUrl()) {
            $this->asset->setBaseUrl(site_url('/'));
        }

        if (!$this->asset->getRelPrefix()) {
            $this->asset->setRelPrefix($this->app->httpRequest()->getRewriteBase());
        }

        global $locale;

        $this->asset->addGlobalJsVar('abspath', ABSPATH);
        $this->asset->addGlobalJsVar('url', site_url('/'));
        $this->asset->addGlobalJsVar('rel', $this->app->httpRequest()->getRewriteBase());
        $this->asset->addGlobalJsVar('locale', $locale);

        add_action('wp_head', function () {
            echo $this->asset->headerStyles();
            echo $this->asset->headerScripts();
        }, 5);

        add_action('wp_footer', function () {
            echo $this->asset->footerScripts();
        }, 5);

        add_action('admin_print_styles', function () {
            echo $this->asset->headerStyles();
        });

        add_action('admin_print_scripts', function () {
            echo $this->asset->headerScripts();
        });

        add_action('admin_print_footer_scripts',function () {
            echo $this->asset->footerScripts();
        });
    }
}
