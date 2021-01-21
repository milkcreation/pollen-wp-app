<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Pollen\WpApp\Container\Container;
use Pollen\WpApp\Http\HttpServiceProvider;
use Pollen\WpApp\Http\RequestInterface;
use Pollen\WpApp\Support\Concerns\BootableTrait;
use Pollen\WpApp\Support\Concerns\ConfigBagTrait;
use Pollen\WpApp\Support\Concerns\ContainerAwareTrait;
use Pollen\WpApp\Routing\RoutingServiceProvider;
use Pollen\WpApp\Routing\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use RuntimeException;

class WpApp implements WpAppInterface
{
    use BootableTrait;
    use ContainerAwareTrait;
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
        RoutingServiceProvider::class
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!defined('WPINC')) {
            throw new RuntimeException('Wordpress must be installed to work');
        }

        $this->setConfig($config);

        add_action('after_setup_theme', function () {
            $this->boot();
        });

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
            $this->setContainer(new Container());

            foreach ($this->serviceProviders as $serviceProvider) {
                $this->getContainer()->setProvider($serviceProvider);
            }
            $this->getContainer()->boot();

            $this->setBooted();
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return ContainerInterface|Container|null
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function psrRequest(): ?PsrRequest
    {
        if ($this->containerHas(PsrRequest::class)) {
            return $this->containerGet(PsrRequest::class);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function request(): ?RequestInterface
    {
        if ($this->containerHas(RequestInterface::class)) {
            return $this->containerGet(RequestInterface::class);
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function router(): ?RouterInterface
    {
        if ($this->containerHas(RouterInterface::class)) {
            return $this->containerGet(RouterInterface::class);
        }
        return null;
    }
}