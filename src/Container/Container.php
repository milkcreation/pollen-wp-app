<?php

declare(strict_types=1);

namespace Pollen\WpApp\Container;

use Error;
use League\Container\Container as BaseContainer;
use Psr\Container\ContainerInterface;
use RuntimeException;

class Container extends BaseContainer
{
    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [];

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): self
    {
        if ($this->booted === false) {
            $this->share(ContainerInterface::class, $this);

            $serviceProviders = [];

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

            $this->booted = true;
        }
        return $this;
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