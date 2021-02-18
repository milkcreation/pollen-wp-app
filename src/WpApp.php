<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Exception;
use League\Container\ReflectionContainer;
use Pollen\Container\Container;
use Pollen\Container\ServiceProviderInterface;
use Pollen\Cookie\CookieJarInterface;
use Pollen\Cookie\CookieServiceProvider;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Debug\DebugServiceProvider;
use Pollen\Encryption\EncrypterInterface;
use Pollen\Encryption\EncryptionServiceProvider;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Event\EventServiceProvider;
use Pollen\Http\HttpServiceProvider;
use Pollen\Log\LogManagerInterface;
use Pollen\Log\LogServiceProvider;
use Pollen\Partial\PartialInterface;
use Pollen\Partial\PartialServiceProvider;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagTrait;
use Pollen\Support\DateTime;
use Pollen\Routing\RoutingServiceProvider;
use Pollen\Routing\RouterInterface;
use Pollen\Validation\ValidationServiceProvider;
use Pollen\Validation\ValidatorInterface;
use Pollen\WpApp\Debug\Debug;
use Pollen\WpApp\Routing\Routing;
use Pollen\WpApp\Post\PostQuery;
use Pollen\WpApp\Post\PostQueryInterface;
use Pollen\WpApp\Term\TermQuery;
use Pollen\WpApp\Term\TermQueryInterface;
use Pollen\WpApp\User\UserQuery;
use Pollen\WpApp\User\UserQueryInterface;
use Pollen\WpApp\User\UserRoleManagerInterface;
use Pollen\WpApp\User\UserServiceProvider;
use Psr\Container\ContainerInterface;
use RuntimeException;

class WpApp extends Container implements WpAppInterface
{
    use BootableTrait;
    use ConfigBagTrait;

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
        CookieServiceProvider::class,
        DebugServiceProvider::class,
        EncryptionServiceProvider::class,
        EventServiceProvider::class,
        HttpServiceProvider::class,
        LogServiceProvider::class,
        PartialServiceProvider::class,
        RoutingServiceProvider::class,
        UserServiceProvider::class,
        ValidationServiceProvider::class,
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

        $this->setConfig($config)->boot();

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

            if ($this->has(DebugManagerInterface::class)) {
                new Debug($this->get(DebugManagerInterface::class), $this);
            }

            if ($router = $this->router()) {
                new Routing($router, $this);
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
        $this->delegate((new ReflectionContainer())->cacheResolutions());

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

            $bootableServiceProviders[] = $serviceProvider->setContainer($this);
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
    public function cookie(): CookieJarInterface
    {
        if ($this->has(CookieJarInterface::class)) {
            return $this->get(CookieJarInterface::class);
        }
        throw new RuntimeException('Unresolvable CookieJar service');
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
        throw new RuntimeException('Unresolvable Encryption service');
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
        throw new RuntimeException('Unresolvable Encryption service');
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
    public function partial(?string $alias = null, $idOrParams = null, array $params = [])
    {
        if ($this->has(PartialInterface::class)) {
            $manager = $this->get(PartialInterface::class);

            return $alias ? $manager->get($alias, $idOrParams, $params) : $manager;
        }
        throw new RuntimeException('Unresolvable Partial service');
    }

    /**
     * @inheritDoc
     */
    public function post($post = null): ?PostQueryInterface
    {
        return PostQuery::create($post);
    }

    /**
     * @inheritDoc
     */
    public function posts($query = null): array
    {
        return PostQuery::fetch($query);
    }

    /**
     * @inheritDoc
     */
    public function role(): UserRoleManagerInterface
    {
        if ($this->has(UserRoleManagerInterface::class)) {
            return $this->get(UserRoleManagerInterface::class);
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
    public function term($term = null): ?TermQueryInterface
    {
        return TermQuery::create($term);
    }

    /**
     * @inheritDoc
     */
    public function terms($query): array
    {
        return TermQuery::fetch($query);
    }

    /**
     * @inheritDoc
     */
    public function user($id = null): ?UserQueryInterface
    {
        return UserQuery::create($id);
    }

    /**
     * @inheritDoc
     */
    public function users($query): array
    {
        return UserQuery::fetch($query);
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