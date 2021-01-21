<?php

declare(strict_types=1);

namespace Pollen\WpApp\Partial;

use Pollen\WpApp\Support\Concerns\ParamsBagTrait;
use Pollen\WpApp\View\ViewEngine;
use Pollen\WpApp\View\ViewEngineInterface;

abstract class AbstractPartialDriver
{
    use ParamsBagTrait;

    /**
     * @var ViewEngineInterface|null
     */
    protected $viewEngine;

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
        return $this->view('index', $this->params()->all());
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
        } else {
            return $this->viewEngine->render($name, $data);
        }
    }

    /**
     * Chemin absolu vers le répertoire de gabarits.
     *
     * @return string
     */
    abstract public function viewDirectory(): string;
}