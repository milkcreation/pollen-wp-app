<?php

declare(strict_types=1);

namespace Pollen\WpApp\Support;

class HtmlAttrs
{
    /**
     * Liste des attributs HTML.
     * @var array
     */
    protected $attributes = [];

    /**
     * Convertion d'une liste d'attributs en attributs HTML.
     *
     * @param array $attrs Liste des attributs.
     *
     * @return void
     */
    public function __construct(array $attrs)
    {
        $this->set($attrs);
    }

    /**
     * Récupération de la liste des attributs sous la forme d'une chaine de caractères.
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode(' ', $this->attributes);
    }

    /**
     * Convertion|Récupération d'attributs HTML
     *
     * @param array $attrs
     * @param bool $linearized
     *
     * @return string|array
     */
    public static function createFromAttrs(array $attrs, bool $linearized = true)
    {
        $self = new static($attrs);

        return $linearized ? (string)$self : (array)$self->attributes;
    }

    /**
     * Récupération de la liste des attributs définis.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * Encodage d'une valeur de type array.
     * {@internal La valeur de retour par defaut est exploitable en JS avec JSON.parse(decodeURIComponent({{value}}).}
     *
     * @param array $value
     *
     * @return string
     */
    public function arrayEncode(array $value): string
    {
        return rawurlencode(json_encode($value));
    }

    /**
     * Définition d'une liste d'attributs.
     *
     * @param array $attrs Liste des attributs.
     *
     * @return static
     */
    public function set(array $attrs): self
    {
        array_walk($attrs, [$this, 'walk']);

        return $this;
    }

    /**
     * Convertion d'un d'attribut en attribut HTML.
     *
     * @param string|array $value Valeur de l'attribut.
     * @param int|string $key Clé d'indice de l'attribut.
     *
     * @return void
     */
    protected function walk($value, $key = null): void
    {
        if (is_array($value)) {
            $value = $this->arrayEncode($value);
        }
        $this->attributes[] = is_numeric($key) ? "{$value}" : "{$key}=\"{$value}\"";
    }
}