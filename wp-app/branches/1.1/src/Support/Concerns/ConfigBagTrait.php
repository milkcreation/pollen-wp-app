<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support\Concerns;

use Pollen\WpApp\Support\ParamsBag;
use InvalidArgumentException;

trait ConfigBagTrait
{
    /**
     * Instance du gestionnaire de paramètres de configuration.
     * @var ParamsBag|null
     */
    protected $configBag;

    /**
     * Liste des paramètres de configuration par défaut.
     *
     * @return array
     */
    public function defaultConfig(): array
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
    public function config($key = null, $default = null)
    {
        if (!$this->configBag instanceof ParamsBag) {
            $this->configBag = ParamsBag::createFromAttrs($this->defaultConfig());
        }

        if (is_null($key)) {
            return $this->configBag;
        } elseif (is_string($key)) {
            return $this->configBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->configBag->set($key);
        } else {
            throw new InvalidArgumentException('Invalid ConfigBag passed method arguments');
        }
    }

    /**
     * Traitement de la liste des paramètres de configuration.
     *
     * @return static
     */
    public function parseConfig(): self
    {
        return $this;
    }

    /**
     * Définition de la liste des paramètres de configuration.
     *
     * @param array $params
     *
     * @return static
     */
    public function setConfig(array $params): self
    {
        $this->config($params);

        return $this;
    }
}