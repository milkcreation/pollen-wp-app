<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Exception;
use Pollen\Asset\AssetManagerInterface;
use Pollen\Asset\AssetServiceProvider;
use Pollen\Container\Container;
use Pollen\Container\ServiceProviderInterface;
use Pollen\Cookie\CookieJarInterface;
use Pollen\Cookie\CookieServiceProvider;
use Pollen\Database\DatabaseManagerInterface;
use Pollen\Database\DatabaseServiceProvider;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Debug\DebugServiceProvider;
use Pollen\Encryption\EncrypterInterface;
use Pollen\Encryption\EncryptionServiceProvider;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Event\EventServiceProvider;
use Pollen\Field\FieldManagerInterface;
use Pollen\Field\FieldServiceProvider;
use Pollen\Filesystem\FilesystemServiceProvider;
use Pollen\Filesystem\StorageManagerInterface;
use Pollen\Form\FormManagerInterface;
use Pollen\Form\FormServiceProvider;
use Pollen\Http\HttpServiceProvider;
use Pollen\Http\RequestInterface;
use Pollen\Log\LogManagerInterface;
use Pollen\Log\LogServiceProvider;
use Pollen\Mail\MailManagerInterface;
use Pollen\Mail\MailServiceProvider;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Partial\PartialServiceProvider;
use Pollen\Session\SessionManagerInterface;
use Pollen\Session\SessionServiceProvider;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\DateTime;
use Pollen\Routing\RoutingServiceProvider;
use Pollen\Routing\RouterInterface;
use Pollen\Validation\ValidationServiceProvider;
use Pollen\Validation\ValidatorInterface;
use Pollen\WpApp\Asset\Asset;
use Pollen\WpApp\Cookie\CookieJar;
use Pollen\WpApp\Database\Database;
use Pollen\WpApp\Debug\Debug;
use Pollen\WpApp\Mail\Mail;
use Pollen\WpApp\Routing\Routing;
use Pollen\WpHook\WpHookerInterface;
use Pollen\WpHook\WpHookServiceProvider;
use Pollen\WpPost\WpPostQuery;
use Pollen\WpPost\WpPostQueryInterface;
use Pollen\WpPost\WpPostServiceProvider;
use Pollen\WpTaxonomy\WpTermQuery;
use Pollen\WpTaxonomy\WpTermQueryInterface;
use Pollen\WpUser\WpUserQuery;
use Pollen\WpUser\WpUserQueryInterface;
use Pollen\WpUser\WpUserRoleManagerInterface;
use Pollen\WpUser\WpUserServiceProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use RuntimeException;

class WpApp extends Container implements WpAppInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;

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
        WpHookServiceProvider::class,
        WpPostServiceProvider::class,
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
        throw new RuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): WpAppInterface
    {
        if (!$this->isBooted()) {
            $this->bootContainer();

            global $locale;
            DateTime::setLocale($locale);

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
                } catch (RuntimeException $e) {
                    //throw $e;
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
            $bootableServiceProviders[] = $serviceProvider;
            $this->addServiceProvider($serviceProvider);
        }

        /** @var ServiceProviderInterface $serviceProvider */
        foreach ($bootableServiceProviders as $serviceProvider) {
            $serviceProvider->boot();
        }
    }

    /**
     * @inheritDoc
     */
    public function asset(?string $name = null)
    {
        if ($this->has(AssetManagerInterface::class)) {
            /** @var AssetManagerInterface $manager */
            $manager = $this->get(AssetManagerInterface::class);

            return $name === null ? $manager : $manager->get($name);
        }
        throw new RuntimeException('Unresolvable Asset Manager service');
    }

    /**
     * @inheritDoc
     */
    public function cookie(?string $alias = null, array $args = [])
    {
        if ($this->has(CookieJarInterface::class)) {
            /** @var CookieJarInterface $cookieJar */
            $cookieJar = $this->get(CookieJarInterface::class);

            return $alias === null ? $cookieJar : $cookieJar->make($alias, $args);
        }
        throw new RuntimeException('Unresolvable CookieJar service');
    }

    /**
     * @inheritDoc
     */
    public function db(?string $table = null)
    {
        if ($this->has(DatabaseManagerInterface::class)) {
            /** @var DatabaseManagerInterface $manager */
            $manager = $this->get(DatabaseManagerInterface::class);

            if ($table === null) {
                return $manager;
            }
            return $manager->getConnection()->table($table);
        }
        throw new RuntimeException('Unresolvable Database service');
    }

    /**
     * @inheritDoc
     */
    public function debug(): DebugManagerInterface
    {
        if ($this->has(DebugManagerInterface::class)) {
            return $this->get(DebugManagerInterface::class);
        }
        throw new RuntimeException('Unresolvable Debug service');
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $hash): string
    {
        if ($this->has(EncrypterInterface::class)) {
            /** @var EncrypterInterface $encrypter */
            $encrypter = $this->get(EncrypterInterface::class);

            return $encrypter->decrypt($hash);
        }
        throw new RuntimeException('Unresolvable Encrypter service');
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $plain): string
    {
        if ($this->has(EncrypterInterface::class)) {
            /** @var EncrypterInterface $encrypter */
            $encrypter = $this->get(EncrypterInterface::class);

            return $encrypter->encrypt($plain);
        }
        throw new RuntimeException('Unresolvable Encrypter service');
    }

    /**
     * @inheritDoc
     */
    public function event(): EventDispatcherInterface
    {
        if ($this->has(EventDispatcherInterface::class)) {
            return $this->get(EventDispatcherInterface::class);
        }
        throw new RuntimeException('Unresolvable Event service');
    }

    /**
     * @inheritDoc
     */
    public function field(?string $alias = null, $idOrParams = null, array $params = [])
    {
        if ($this->has(FieldManagerInterface::class)) {
            /** @var FieldManagerInterface $manager */
            $manager = $this->get(FieldManagerInterface::class);

            return $alias !== null ? $manager->get($alias, $idOrParams, $params) : $manager;
        }
        throw new RuntimeException('Unresolvable Field service');
    }

    /**
     * @inheritDoc
     */
    public function form(?string $alias = null)
    {
        if ($this->has(FormManagerInterface::class)) {
            /** @var FormManagerInterface $manager */
            $manager = $this->get(FormManagerInterface::class);

            return $alias !== null ? $manager->get($alias) : $manager;
        }
        throw new RuntimeException('Unresolvable Form Manager service');
    }

    /**
     * @inheritDoc
     */
    public function hook(?string $hook = null)
    {
        if ($this->has(WpHookerInterface::class)) {
            /** @var WpHookerInterface $hooker */
            $manager = $this->get(WpHookerInterface::class);

            return $hook !== null ? $manager->get($hook) : $manager;
        }

        throw new RuntimeException('Unresolvable WpHooker service');
    }

    /**
     * @inheritDoc
     */
    public function log(
        ?string $message = null,
        $level = null,
        array $context = [],
        ?string $channel = null
    ): ?LogManagerInterface {
        if ($this->has(LogManagerInterface::class)) {
            /** @var LogManagerInterface $manager */
            $manager = $this->get(LogManagerInterface::class);

            if ($message === null) {
                return $manager;
            }

            $logger = ($channel !== null) ? $manager->channel($channel) : $manager;

            $logger->log($level ?? 'ERROR', $message, $context);

            return null;
        }
        throw new RuntimeException('Unresolvable Log service');
    }

    /**
     * @inheritDoc
     */
    public function mail($mailable = null)
    {
        if ($this->has(MailManagerInterface::class)) {
            /** @var MailManagerInterface $manager */
            $manager = $this->get(MailManagerInterface::class);

            return $mailable !== null ? $manager->setMailable($mailable)->getMailable() : $manager;
        }
        throw new RuntimeException('Unresolvable Mail service');
    }

    /**
     * @inheritDoc
     */
    public function partial(?string $alias = null, $idOrParams = null, array $params = [])
    {
        if ($this->has(PartialManagerInterface::class)) {
            /** @var PartialManagerInterface $manager */
            $manager = $this->get(PartialManagerInterface::class);

            return $alias !== null ? $manager->get($alias, $idOrParams, $params) : $manager;
        }
        throw new RuntimeException('Unresolvable Partial service');
    }

    /**
     * @inheritDoc
     */
    public function post($post = null): ?WpPostQueryInterface
    {
        return WpPostQuery::create($post);
    }

    /**
     * @inheritDoc
     */
    public function posts($query = null): array
    {
        return WpPostQuery::fetch($query);
    }

    /**
     * @inheritDoc
     */
    public function psrRequest(): PsrRequest
    {
        if ($this->has(PsrRequest::class)) {
            return $this->get(PsrRequest::class);
        }
        throw new RuntimeException('Unresolvable Psr-7 Http Request service');
    }

    /**
     * @inheritDoc
     */
    public function request(): RequestInterface
    {
        if ($this->has(RequestInterface::class)) {
            return $this->get(RequestInterface::class);
        }
        throw new RuntimeException('Unresolvable Http Request service');
    }

    /**
     * @inheritDoc
     */
    public function role(): WpUserRoleManagerInterface
    {
        if ($this->has(WpUserRoleManagerInterface::class)) {
            return $this->get(WpUserRoleManagerInterface::class);
        }
        throw new RuntimeException('Unresolvable UserRole service');
    }

    /**
     * @inheritDoc
     */
    public function router(): RouterInterface
    {
        if ($this->has(RouterInterface::class)) {
            return $this->get(RouterInterface::class);
        }
        throw new RuntimeException('Unresolvable Routing service');
    }

    /**
     * @inheritDoc
     */
    public function storage(?string $name = null)
    {
        if ($this->has(StorageManagerInterface::class)) {
            $manager = $this->get(StorageManagerInterface::class);

            return $name ? $manager->disk($name) : $manager;
        }
        throw new RuntimeException('Unresolvable Filesystem service');
    }

    /**
     * @inheritDoc
     */
    public function term($term = null): ?WpTermQueryInterface
    {
        return WpTermQuery::create($term);
    }

    /**
     * @inheritDoc
     */
    public function terms($query): array
    {
        return WpTermQuery::fetch($query);
    }

    /**
     * @inheritDoc
     */
    public function user($id = null): ?WpUserQueryInterface
    {
        return WpUserQuery::create($id);
    }

    /**
     * @inheritDoc
     */
    public function users($query): array
    {
        return WpUserQuery::fetch($query);
    }

    /**
     * @inheritDoc
     */
    public function validator(): ValidatorInterface
    {
        if ($this->has(ValidatorInterface::class)) {
            return $this->get(ValidatorInterface::class);
        }
        throw new RuntimeException('Unresolvable Validation service');
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