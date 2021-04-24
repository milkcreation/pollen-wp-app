<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Pollen\Container\ContainerInterface;
use Pollen\Debug\DebugProxyInterface;
use Pollen\Encryption\EncrypterProxyInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\Proxy\AssetProxyInterface;
use Pollen\Support\Proxy\CookieProxyInterface;
use Pollen\Support\Proxy\DbProxyInterface;
use Pollen\Support\Proxy\EventProxyInterface;
use Pollen\Support\Proxy\FieldProxyInterface;
use Pollen\Support\Proxy\FormProxyInterface;
use Pollen\Support\Proxy\HttpRequestProxyInterface;
use Pollen\Support\Proxy\LogProxyInterface;
use Pollen\Support\Proxy\MailProxyInterface;
use Pollen\Support\Proxy\PartialProxyInterface;
use Pollen\Support\Proxy\RouterProxyInterface;
use Pollen\Support\Proxy\SessionProxyInterface;
use Pollen\Support\Proxy\StorageProxyInterface;
use Pollen\Support\Proxy\ValidatorProxyInterface;
use Pollen\WpHook\WpHookerProxyInterface;
use Pollen\WpPost\WpPostProxyInterface;
use Pollen\WpTaxonomy\WpTaxonomyProxyInterface;
use Pollen\WpUser\WpUserProxyInterface;

interface WpAppInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerInterface,
    AssetProxyInterface,
    CookieProxyInterface,
    DbProxyInterface,
    DebugProxyInterface,
    EncrypterProxyInterface,
    EventProxyInterface,
    FieldProxyInterface,
    FormProxyInterface,
    HttpRequestProxyInterface,
    LogProxyInterface,
    MailProxyInterface,
    PartialProxyInterface,
    RouterProxyInterface,
    SessionProxyInterface,
    StorageProxyInterface,
    ValidatorProxyInterface,
    WpHookerProxyInterface,
    WpPostProxyInterface,
    WpTaxonomyProxyInterface,
    WpUserProxyInterface
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     */
    public static function instance(): WpAppInterface;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): WpAppInterface;

    /**
     * Initialisation du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function bootContainer(): void;
}