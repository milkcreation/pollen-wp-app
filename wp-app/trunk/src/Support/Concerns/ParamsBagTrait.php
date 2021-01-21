<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support\Concerns;

use Pollen\WpApp\Support\ParamsBag;
use InvalidArgumentException;

trait ParamsBagTrait
{
    /**
     * Instance du gestionnaire de paramètres
     * @var ParamsBag|null
     */
    protected $paramsBag;

    /**
     * Liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaultParams(): array
    {
        return [];
    }

    /**
     * Définition|Récupération|Instance des paramètres de configuration.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|int|array|mixed|ParamsBag
     *
     * @throws InvalidArgumentException
     */
    public function params($key = null, $default = null)
    {
        if (!$this->paramsBag instanceof ParamsBag) {
            $this->paramsBag = ParamsBag::createFromAttrs($this->defaultParams());
        }

        if (is_null($key)) {
            return $this->paramsBag;
        } elseif (is_string($key)) {
            return $this->paramsBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->paramsBag->set($key);
        } else {
            throw new InvalidArgumentException('Invalid ParamsBag passed method arguments');
        }
    }

    /**
     * Traitement de la liste des paramètres.
     *
     * @return static
     */
    public function parseParams(): self
    {
        return $this;
    }

    /**
     * Définition de la liste des paramètres.
     *
     * @param array $params
     *
     * @return static
     */
    public function setParams(array $params): self
    {
        $this->params($params);

        return $this;
    }
}