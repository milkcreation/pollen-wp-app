<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use League\Container\ReflectionContainer;
use Pollen\Container\Container;
use Pollen\Container\ServiceProviderInterface;
use Pollen\Http\HttpServiceProvider;
use Pollen\Http\RequestInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagTrait;
use Pollen\Support\DateTime;
use Pollen\Routing\RoutingServiceProvider;
use Pollen\Routing\RouterInterface;
use Pollen\Validation\ValidationServiceProvider;
use Pollen\Validation\ValidatorInterface;
use Pollen\WpApp\Post\PostQuery;
use Pollen\WpApp\Post\PostQueryInterface;
use Pollen\WpApp\Term\TermQuery;
use Pollen\WpApp\Term\TermQueryInterface;
use Pollen\WpApp\User\UserQuery;
use Pollen\WpApp\User\UserQueryInterface;
use Pollen\WpApp\User\UserRoleManagerInterface;
use Pollen\WpApp\User\UserServiceProvider;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use RuntimeException;
use Throwable;

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
     * Fournisseurs de services du conteneur d'injectections de dépendances
     * @var string[]
     */
    protected $serviceProviders = [
        HttpServiceProvider::class,
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
            $this->delegate((new ReflectionContainer())->cacheResolutions());

            $this->share('config', $this->config());

            $this->serviceProviders += $this->config('service-providers', []);

            foreach ($this->serviceProviders as $definition) {
                if (is_string($definition)) {
                    try {
                        $serviceProvider = new $definition();
                    } catch (Throwable $e) {
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

                $serviceProviders[] = $serviceProvider->setContainer($this);
                $this->addServiceProvider($serviceProvider);
            }

            array_walk(
                $serviceProviders,
                function (ServiceProviderInterface $serviceProvider) {
                    $serviceProvider->boot();
                }
            );

            global $locale;
            DateTime::setLocale($locale);

            $this->setBooted();
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function psrRequest(): ?PsrRequest
    {
        if ($this->has(PsrRequest::class)) {
            return $this->get(PsrRequest::class);
        }
        return null;
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
    public function request(): ?RequestInterface
    {
        if ($this->has(RequestInterface::class)) {
            return $this->get(RequestInterface::class);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function role(): ?UserRoleManagerInterface
    {
        if ($this->has(UserRoleManagerInterface::class)) {
            return $this->get(UserRoleManagerInterface::class);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function router(): ?RouterInterface
    {
        if ($this->has(RouterInterface::class)) {
            return $this->get(RouterInterface::class);
        }
        return null;
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
    public function validator(): ?ValidatorInterface
    {
        if ($this->has(ValidatorInterface::class)) {
            return $this->get(ValidatorInterface::class);
        }
        return null;
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