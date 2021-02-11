<?php

declare(strict_types=1);

namespace Pollen\WpApp\Partial;

use BadMethodCallException;
use Pollen\Support\Concerns\ParamsBagTrait;
use Pollen\View\ViewEngine;
use Pollen\View\ViewEngineInterface;
use Throwable;

/**
 * @mixin \Pollen\Support\ParamsBag
 */
abstract class AbstractPartialDriver
{
    use ParamsBagTrait;

    /**
     * @var ViewEngineInterface|null
     */
    protected $viewEngine;

    /**
     * Délégation d'appel des méthodes du ParamBag.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->params()->{$method}(...$arguments);
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    'PartialDriver [%s] method call [%s] throws an exception: %s',
                    get_class(),
                    $method,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Résolution de sortie de la classe sous forme de chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Rendu d'affichage.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->view('index', $this->all());
    }

    /**
     * Générateur de gabarits d'affichage.
     *
     * @param string|null $name
     * @param array $data
     *
     * @return ViewEngineInterface|string|null
     */
    public function view(?string $name = null, array $data = [])
    {
        if ($this->viewEngine === null) {
            $this->viewEngine = new ViewEngine($this->viewDirectory());
        }

        if ($name === null) {
            return $this->viewEngine;
        }
        return $this->viewEngine->render($name, $data);
    }

    /**
     * Chemin absolu vers le répertoire de gabarits.
     *
     * @return string
     */
    abstract public function viewDirectory(): string;
}