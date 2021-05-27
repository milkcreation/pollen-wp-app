<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Exception;
use Pollen\Asset\AssetServiceProvider;
use Pollen\Container\BootableServiceProviderInterface;
use Pollen\Container\Container;
use Pollen\Container\ServiceProviderInterface;
use Pollen\Cookie\CookieServiceProvider;
use Pollen\Database\DatabaseServiceProvider;
use Pollen\Debug\DebugServiceProvider;
use Pollen\Encryption\EncryptionServiceProvider;
use Pollen\Event\EventServiceProvider;
use Pollen\Field\FieldServiceProvider;
use Pollen\Filesystem\FilesystemServiceProvider;
use Pollen\Form\FormServiceProvider;
use Pollen\Http\HttpServiceProvider;
use Pollen\Log\LogServiceProvider;
use Pollen\Mail\MailServiceProvider;
use Pollen\Partial\PartialServiceProvider;
use Pollen\Session\SessionManagerInterface;
use Pollen\Session\SessionServiceProvider;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ResourcesAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Routing\RoutingServiceProvider;
use Pollen\Support\Proxy\AssetProxy;
use Pollen\Support\Proxy\CookieProxy;
use Pollen\Support\Proxy\DbProxy;
use Pollen\Support\Proxy\DebugProxy;
use Pollen\Support\Proxy\EncrypterProxy;
use Pollen\Support\Proxy\EventProxy;
use Pollen\Support\Proxy\FieldProxy;
use Pollen\Support\Proxy\FormProxy;
use Pollen\Support\Proxy\HttpRequestProxy;
use Pollen\Support\Proxy\LogProxy;
use Pollen\Support\Proxy\MailProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\Support\Proxy\RouterProxy;
use Pollen\Support\Proxy\SessionProxy;
use Pollen\Support\Proxy\StorageProxy;
use Pollen\Support\Proxy\ValidatorProxy;
use Pollen\Support\ProxyResolver;
use Pollen\Validation\ValidationServiceProvider;
use Pollen\View\ViewServiceProvider;
use Pollen\WpApp\Date\Date;
use Pollen\WpApp\Asset\Asset;
use Pollen\WpApp\Cookie\CookieJar;
use Pollen\WpApp\Database\Database;
use Pollen\WpApp\Debug\Debug;
use Pollen\WpApp\Mail\Mail;
use Pollen\WpApp\Middleware\WpAdminMiddleware;
use Pollen\WpApp\Routing\Routing;
use Pollen\WpHook\WpHookerProxy;
use Pollen\WpHook\WpHookServiceProvider;
use Pollen\WpPost\WpPostProxy;
use Pollen\WpPost\WpPostServiceProvider;
use Pollen\WpTerm\WpTermProxy;
use Pollen\WpTerm\WpTermServiceProvider;
use Pollen\WpUser\WpUserProxy;
use Pollen\WpUser\WpUserServiceProvider;
use Psr\Container\ContainerInterface;
use RuntimeException;

class WpApp extends Container implements WpAppInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ResourcesAwareTrait;
    use AssetProxy;
    use CookieProxy;
    use DbProxy;
    use DebugProxy;
    use EncrypterProxy;
    use EventProxy;
    use FieldProxy;
    use FormProxy;
    use HttpRequestProxy;
    use LogProxy;
    use MailProxy;
    use PartialProxy;
    use RouterProxy;
    use SessionProxy;
    use StorageProxy;
    use ValidatorProxy;
    use WpHookerProxy;
    use WpPostProxy;
    use WpTermProxy;
    use WpUserProxy;

    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Fournisseurs de services du conteneur d'injections de dépendances
     * @var string[]
     */
    protected $serviceProviders = [
        AssetServiceProvider::class,
        CookieServiceProvider::class,
        DatabaseServiceProvider::class,
        DebugServiceProvider::class,
        EncryptionServiceProvider::class,
        EventServiceProvider::class,
        FieldServiceProvider::class,
        FilesystemServiceProvider::class,
        FormServiceProvider::class,
        HttpServiceProvider::class,
        LogServiceProvider::class,
        MailServiceProvider::class,
        PartialServiceProvider::class,
        RoutingServiceProvider::class,
        SessionServiceProvider::class,
        ValidationServiceProvider::class,
        ViewServiceProvider::class,
        WpHookServiceProvider::class,
        WpPostServiceProvider::class,
        WpTermServiceProvider::class,
        WpUserServiceProvider::class,
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!defined('WPINC')) {
            throw new RuntimeException('Wordpress must be installed to work');
        }

        parent::__construct();

        $this->share(ContainerInterface::class, $this);
        $this->share(WpAppInterface::class, $this);

        $this->setResourcesBaseDir(dirname(__DIR__) . '/resources');

        $this->setConfig($config);
        $this->boot();

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): WpAppInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): WpAppInterface
    {
        if (!$this->isBooted()) {
            $this->add('routing.middleware.wp-admin', function () {
                return new WpAdminMiddleware();
            });

            $this->bootContainer();

            new Date($this);

            try {
                $debug = $this->debug();
                new Debug($debug, $this);
            } catch (RuntimeException $e) {
                unset($e);
            }

            if ($this->has(SessionManagerInterface::class)) {
                /** @var SessionManagerInterface $session */
                $session = $this->get(SessionManagerInterface::class);

                try {
                    $session->start();

                    $this->httpRequest()->setSession($session->processor());
                } catch (RuntimeException $e) {
                    unset($e);
                }
            }

            try {
                $db = $this->db();
                new Database($db, $this);
            } catch (RuntimeException $e) {
                unset($e);
            }

            try {
                $router = $this->router();
                new Routing($router, $this);
            } catch (RuntimeException $e) {
                unset($e);
            }

            try {
                $asset = $this->asset();
                new Asset($asset, $this);
            } catch (RuntimeException $e) {
                unset($e);
            }

            try {
                $cookieJar = $this->cookie();
                new CookieJar($cookieJar, $this);
            } catch (RuntimeException $e) {
                unset($e);
            }

            try {
                $mailManager = $this->mail();
                new Mail($mailManager, $this);
            } catch (RuntimeException $e) {
                unset($e);
            }

            $this->setBooted();
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function bootContainer(): void
    {
        $this->enableAutoWiring(true);

        ProxyResolver::setContainer($this);

        $this->share('config', $this->config());

        $this->serviceProviders = array_merge($this->config('service-providers', []), $this->serviceProviders);
        $bootableServiceProviders = [];

        foreach ($this->serviceProviders as $definition) {
            if (is_string($definition)) {
                try {
                    $serviceProvider = new $definition();
                } catch (Exception $e) {
                    throw new RuntimeException(
                        'ServiceProvider [%s] instanciation return exception :%s',
                        $definition,
                        $e->getMessage()
                    );
                }
            } elseif (is_object($definition)) {
                $serviceProvider = $definition;
            } else {
                throw new RuntimeException(
                    'ServiceProvider [%s] type not supported',
                    $definition
                );
            }

            if (!$serviceProvider instanceof ServiceProviderInterface) {
                throw new RuntimeException(
                    'ServiceProvider [%s] must be an instance of %s',
                    $definition,
                    ServiceProviderInterface::class
                );
            }

            $serviceProvider->setContainer($this);
            if ($serviceProvider instanceof BootableServiceProviderInterface) {
                $bootableServiceProviders[] = $serviceProvider;
            }
            $this->addServiceProvider($serviceProvider);
        }

        /** @var BootableServiceProviderInterface $serviceProvider */
        foreach ($bootableServiceProviders as $serviceProvider) {
            $serviceProvider->boot();
        }
    }

    /**
     * Déclaration d'un fournisseur de service.
     *
     * @param string|ServiceProviderInterface $serviceProviderDefinition
     *
     * @return static
     */
    public function setProvider($serviceProviderDefinition): self
    {
        $this->serviceProviders[] = $serviceProviderDefinition;

        return $this;
    }
}