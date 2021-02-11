<?php

declare(strict_types=1);

namespace Pollen\WpApp\Validation;

use Respect\Validation\Validatable;

interface ValidationRuleInterface extends Validatable
{
    /**
     * Définition de la liste des arguments.
     *
     * @param array ...$args
     *
     * @return static
     */
    public function setArgs(...$args): ValidationRuleInterface;
}