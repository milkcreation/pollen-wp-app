<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Error;
use League\Container\ReflectionContainer;
use Pollen\WpApp\Container\Container;
use Pollen\WpApp\Container\ServiceProviderInterface;
use Pollen\WpApp\Http\HttpServiceProvider;
use Pollen\WpApp\Http\RequestInterface;
use Pollen\WpApp\Support\Concerns\BootableTrait;
use Pollen\WpApp\Support\Concerns\ConfigBagTrait;
use Pollen\WpApp\Support\DateTime;
use Pollen\WpApp\Routing\RoutingServiceProvider;
use Pollen\WpApp\Routing\RouterInterface;
use Pollen\WpApp\Validation\ValidationServiceProvider;
use Pollen\WpApp\Validation\ValidatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
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
     * Fournisseurs de services du conteneur d'injectections de dépendances
     * @var string[]
     */
    protected $serviceProviders = [
        HttpServiceProvider::class,
        RoutingServiceProvider::class,
        ValidationServiceProvider::class
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

            foreach($this->serviceProviders as $definition) {
                if (is_string($definition)) {
                    try {
                        $serviceProvider = new $definition();
                    } catch (Error $e) {
                        throw new RuntimeException(
                            'ServiceProvider [%s] instanciation return exception :%s',
                            $definition,
                            $e->getMessage()
                        );
                    }
                } elseif (is_object($definition)){
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
                } else {
                    $serviceProviders[] = $serviceProvider->setContainer($this);
                    $this->addServiceProvider($serviceProvider);
                }
            }

            array_walk($serviceProviders, function (ServiceProviderInterface $serviceProvider){
                $serviceProvider->boot();
            });

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