<?php

declare(strict_types=1);

namespace Pollen\WpApp\Validation\Rules;

use Pollen\WpApp\Validation\ValidationRuleInterface;
use Respect\Validation\Rules\AbstractRule as BaseAbstractRule;

abstract class AbstractRule extends BaseAbstractRule implements ValidationRuleInterface
{
    /**
     * @inheritDoc
     */
    public function setArgs(...$args): ValidationRuleInterface
    {
        return $this;
    }
}