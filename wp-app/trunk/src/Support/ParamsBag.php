<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support;

use Illuminate\Support\Arr;
use ArrayIterator;
use ArrayAccess;
use Countable;
use IteratorAggregate;

class ParamsBag implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Liste des paramètres.
     * @var array
     */
    protected $attributes = [];

    /**
     * Création d'un instance basée sur une liste d'attributs.
     *
     * @param array Liste des attributs.
     *
     * @return static
     */
    public static function createFromAttrs($attrs): self
    {
        return (new static())->set($attrs)->parse();
    }

    /**
     * Récupération d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Définition d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     * @param mixed $value Valeur.
     *
     * @return void
     */
    public function __set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Vérification d'existance d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return boolean
     */
    public function __isset($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Suppression d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return void
     */
    public function __unset($key): void
    {
        $this->offsetUnset($key);
    }

    /**
     * Récupération de la liste des attributs.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * Suppression de la liste des attributs déclarés.
     *
     * @return static
     */
    public function clear(): self
    {
        $this->attributes = [];

        return $this;
    }

    /**
     * Compte le nombre d'éléments.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->attributes);
    }

    /**
     * Définition de la liste des attributs par défaut.
     *
     * @return array
     */
    public function defaults(): array
    {
        return [];
    }

    /**
     * Suppression d'un ou plusieurs attributs.
     *
     * @param array|string $keys Liste des indices des attributs à supprimer. Syntaxe à point permise.
     *
     * @return void
     */
    public function forget($keys): void
    {
        Arr::forget($this->attributes, $keys);
    }

    /**
     * Récupération d'un attribut.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut lorsque l'attribut n'est pas défini.
     *
     * @return mixed
     */
    public function get(string $key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has(string $key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * Récupération de la liste des paramètres au format json.
     * @see http://php.net/manual/fr/function.json-encode.php
     *
     * @param int $options Options d'encodage.
     *
     * @return string
     */
    public function json($options = 0): string
    {
        return json_encode($this->all(), $options);
    }

    /**
     * Récupération de la liste des clés d'indexes des attributs de configuration.
     *
     * @return string[]
     */
    public function keys(): array
    {
        return array_keys($this->attributes);
    }

    /**
     * Cartographie de donnée.
     *
     * @param mixed $value
     * @param string|int $key
     *
     * @return static
     */
    public function map(&$value, $key): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Récupération d'un jeu d'attributs associé à une liste de clés d'indices.
     *
     * @param string[] $keys Liste des clés d'indice du jeu d'attributs à récupérer.
     *
     * @return array
     */
    public function only(array $keys): array
    {
        return Arr::only($this->attributes, $keys);
    }

    /**
     * Traitement de la liste des attributs.
     *
     * @return static
     */
    public function parse(): self
    {
        $this->attributes = array_merge($this->defaults(), $this->attributes);

        return $this;
    }

    /**
     * Récupére la valeur d'un attribut avant de le supprimer.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut lorsque l'attribut n'est pas défini.
     *
     * @return mixed
     */
    public function pull(string $key, $default = null)
    {
        return Arr::pull($this->attributes, $key, $default);
    }

    /**
     * Insertion d'un attribut à la fin d'une liste d'attributs.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return static
     */
    public function push(string $key, $value): self
    {
        if (!$this->has($key)) {
            $this->set($key, []);
        }

        $arr = $this->get($key);

        if (is_array($arr)) {
            array_push($arr, $value);
            $this->set($key, $arr);
        }

        return $this;
    }

    /**
     * Définition d'un attribut.
     *
     * @param string|array $key Clé d'indexe de l'attribut, Syntaxe à point permise ou tableau associatif des attributs
     *                          à définir.
     * @param mixed $value Valeur de l'attribut si la clé d'index est de type string.
     *
     * @return static
     */
    public function set($key, $value = null): self
    {
        $keys = is_array($key) ? $key : [$key => $value];

        array_walk($keys, [$this, 'map']);

        foreach ($keys as $k => $v) {
            Arr::set($this->attributes, $k, $v);
        }

        return $this;
    }

    /**
     * Insertion d'un attribut au début d'une liste d'attributs.
     *
     * @param mixed $value Valeur de l'attribut.
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return static
     */
    public function unshift($value, string $key): self
    {
        if (!$this->has($key)) {
            $this->set($key, []);
        }

        $arr = $this->get($key);

        if (is_array($arr)) {
            array_unshift($arr, $value);
            $this->set($key, $arr);
        }

        return $this;
    }

    /**
     * Récupération de la liste des valeurs des attributs de configuration.
     *
     * @return mixed[]
     */
    public function values(): array
    {
        return array_values($this->attributes);
    }
}