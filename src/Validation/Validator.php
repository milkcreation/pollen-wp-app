<?php

declare(strict_types=1);

namespace Pollen\WpApp\Validation;

use Exception;
use finfo;
use Pollen\WpApp\Validation\Rules\PasswordRule;
use Pollen\WpApp\Validation\Rules\SerializedRule;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Factory;
use Respect\Validation\Rules\AllOf as BaseValidator;
use Respect\Validation\Rules\Key;
use Respect\Validation\Validatable;
use Symfony\Component\Validator\Constraint;

/**
 * @method static ValidatorInterface allOf(Validatable ...$rule)
 * @method static ValidatorInterface alnum(string ...$additionalChars)
 * @method static ValidatorInterface alpha(string ...$additionalChars)
 * @method static ValidatorInterface alwaysInvalid()
 * @method static ValidatorInterface alwaysValid()
 * @method static ValidatorInterface anyOf(Validatable ...$rule)
 * @method static ValidatorInterface arrayType()
 * @method static ValidatorInterface arrayVal()
 * @method static ValidatorInterface attribute(string $reference, Validatable $validator = null, bool $mandatory = true)
 * @method static ValidatorInterface base(int $base, string $chars = null)
 * @method static ValidatorInterface base64()
 * @method static ValidatorInterface between($minimum, $maximum)
 * @method static ValidatorInterface bic(string $countryCode)
 * @method static ValidatorInterface boolType()
 * @method static ValidatorInterface boolVal()
 * @method static ValidatorInterface bsn()
 * @method static ValidatorInterface call(callable $callable, Validatable $rule)
 * @method static ValidatorInterface callableType()
 * @method static ValidatorInterface callback(callable $callback)
 * @method static ValidatorInterface charset(string ...$charset)
 * @method static ValidatorInterface cnh()
 * @method static ValidatorInterface cnpj()
 * @method static ValidatorInterface control(string ...$additionalChars)
 * @method static ValidatorInterface consonant(string ...$additionalChars)
 * @method static ValidatorInterface contains($containsValue, bool $identical = false)
 * @method static ValidatorInterface containsAny(array $needles, bool $strictCompareArray = false)
 * @method static ValidatorInterface countable()
 * @method static ValidatorInterface countryCode(string $set = null)
 * @method static ValidatorInterface currencyCode()
 * @method static ValidatorInterface cpf()
 * @method static ValidatorInterface creditCard(string $brand = null)
 * @method static ValidatorInterface date(string $format = 'Y-m-d')
 * @method static ValidatorInterface dateTime(string $format = null)
 * @method static ValidatorInterface decimal(string ...$additionalChars)
 * @method static ValidatorInterface digit(string ...$additionalChars)
 * @method static ValidatorInterface directory()
 * @method static ValidatorInterface domain(bool $tldCheck = true)
 * @method static ValidatorInterface each(Validatable $rule)
 * @method static ValidatorInterface email()
 * @method static ValidatorInterface endsWith($endValue, bool $identical = false)
 * @method static ValidatorInterface equals($compareTo)
 * @method static ValidatorInterface equivalent($compareTo)
 * @method static ValidatorInterface even()
 * @method static ValidatorInterface executable()
 * @method static ValidatorInterface exists()
 * @method static ValidatorInterface extension(string $extension)
 * @method static ValidatorInterface factor(int $dividend)
 * @method static ValidatorInterface falseVal()
 * @method static ValidatorInterface fibonacci()
 * @method static ValidatorInterface file()
 * @method static ValidatorInterface filterVar(int $filter, $options = null)
 * @method static ValidatorInterface finite()
 * @method static ValidatorInterface floatVal()
 * @method static ValidatorInterface floatType()
 * @method static ValidatorInterface graph(string ...$additionalChars)
 * @method static ValidatorInterface greaterThan($compareTo)
 * @method static ValidatorInterface hexRgbColor()
 * @method static ValidatorInterface iban()
 * @method static ValidatorInterface identical($value)
 * @method static ValidatorInterface image(finfo $fileInfo = null)
 * @method static ValidatorInterface imei()
 * @method static ValidatorInterface in($haystack, bool $compareIdentical = false)
 * @method static ValidatorInterface infinite()
 * @method static ValidatorInterface instance(string $instanceName)
 * @method static ValidatorInterface intVal()
 * @method static ValidatorInterface intType()
 * @method static ValidatorInterface ip(string $range = '*', int $options = null)
 * @method static ValidatorInterface isbn()
 * @method static ValidatorInterface iterableType()
 * @method static ValidatorInterface json()
 * @method static ValidatorInterface key(string $reference, Validatable $referenceValidator = null, bool $mandatory = true)
 * @method static ValidatorInterface keyNested(string $reference, Validatable $referenceValidator = null, bool $mandatory = true)
 * @method static ValidatorInterface keySet(Key ...$rule)
 * @method static ValidatorInterface keyValue(string $comparedKey, string $ruleName, string $baseKey)
 * @method static ValidatorInterface languageCode(string $set = null)
 * @method static ValidatorInterface leapDate(string $format)
 * @method static ValidatorInterface leapYear()
 * @method static ValidatorInterface length(int $min = null, int $max = null, bool $inclusive = true)
 * @method static ValidatorInterface lowercase()
 * @method static ValidatorInterface lessThan($compareTo)
 * @method static ValidatorInterface luhn()
 * @method static ValidatorInterface macAddress()
 * @method static ValidatorInterface max($compareTo)
 * @method static ValidatorInterface maxAge(int $age, string $format = null)
 * @method static ValidatorInterface mimetype(string $mimetype)
 * @method static ValidatorInterface min($compareTo)
 * @method static ValidatorInterface minAge(int $age, string $format = null)
 * @method static ValidatorInterface multiple(int $multipleOf)
 * @method static ValidatorInterface negative()
 * @method static ValidatorInterface nfeAccessKey()
 * @method static ValidatorInterface nif()
 * @method static ValidatorInterface nip()
 * @method static ValidatorInterface no($useLocale = false)
 * @method static ValidatorInterface noneOf(Validatable ...$rule)
 * @method static ValidatorInterface not(Validatable $rule)
 * @method static ValidatorInterface notBlank()
 * @method static ValidatorInterface notEmoji()
 * @method static ValidatorInterface notEmpty()
 * @method static ValidatorInterface notOptional()
 * @method static ValidatorInterface noWhitespace()
 * @method static ValidatorInterface nullable(Validatable $rule)
 * @method static ValidatorInterface nullType()
 * @method static ValidatorInterface number()
 * @method static ValidatorInterface numericVal()
 * @method static ValidatorInterface objectType()
 * @method static ValidatorInterface odd()
 * @method static ValidatorInterface oneOf(Validatable ...$rule)
 * @method static ValidatorInterface optional(Validatable $rule)
 * @method static ValidatorInterface perfectSquare()
 * @method static ValidatorInterface pesel()
 * @method static ValidatorInterface phone()
 * @method static ValidatorInterface phpLabel()
 * @method static ValidatorInterface pis()
 * @method static ValidatorInterface polishIdCard()
 * @method static ValidatorInterface positive()
 * @method static ValidatorInterface postalCode(string $countryCode)
 * @method static ValidatorInterface primeNumber()
 * @method static ValidatorInterface printable(string ...$additionalChars)
 * @method static ValidatorInterface punct(string ...$additionalChars)
 * @method static ValidatorInterface readable()
 * @method static ValidatorInterface regex(string $regex)
 * @method static ValidatorInterface resourceType()
 * @method static ValidatorInterface roman()
 * @method static ValidatorInterface scalarVal()
 * @method static ValidatorInterface sf(Constraint $constraint, ValidatorInterface $validator = null)
 * @method static ValidatorInterface size(string $minSize = null, string $maxSize = null)
 * @method static ValidatorInterface slug()
 * @method static ValidatorInterface sorted(string $direction)
 * @method static ValidatorInterface space(string ...$additionalChars)
 * @method static ValidatorInterface startsWith($startValue, bool $identical = false)
 * @method static ValidatorInterface stringType()
 * @method static ValidatorInterface stringVal()
 * @method static ValidatorInterface subdivisionCode(string $countryCode)
 * @method static ValidatorInterface subset(array $superset)
 * @method static ValidatorInterface symbolicLink()
 * @method static ValidatorInterface time(string $format = 'H:i:s')
 * @method static ValidatorInterface tld()
 * @method static ValidatorInterface trueVal()
 * @method static ValidatorInterface type(string $type)
 * @method static ValidatorInterface unique()
 * @method static ValidatorInterface uploaded()
 * @method static ValidatorInterface uppercase()
 * @method static ValidatorInterface url()
 * @method static ValidatorInterface uuid(int $version = null)
 * @method static ValidatorInterface version()
 * @method static ValidatorInterface videoUrl(string $service = null)
 * @method static ValidatorInterface vowel(string ...$additionalChars)
 * @method static ValidatorInterface when(Validatable $if, Validatable $then, Validatable $when = null)
 * @method static ValidatorInterface writable()
 * @method static ValidatorInterface xdigit(string ...$additionalChars)
 * @method static ValidatorInterface yes($useLocale = false)
 * @method static ValidatorInterface zend($validator, array $params = null)
 * 
 * Personnalisation
 * ---------------------------------------------------------------------------------------------------------------------
 * @method static ValidatorInterface password(array $args = [])
 * @method static ValidatorInterface serialized(bool $strict = true)
 */
class Validator extends BaseValidator implements ValidatorInterface
{
    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Liste des régles personnalisées.
     * @var ValidationRuleInterface[]|array
     */
    protected static $customs = [];

    /**
     * @param Validatable ...$rules
     */
    public function __construct(Validatable ...$rules) {
        parent::__construct(...$rules);

        $customRules = [
            'password'   => new PasswordRule(),
            'serialized' => new SerializedRule(),
        ];
        foreach ($customRules as $name => $rule) {
            $this->setCustomRule($name, $rule);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function __callStatic(string $ruleName, array $arguments): ValidatorInterface
    {
        return self::createOrExisting()->__call($ruleName, $arguments);
    }

    /**
     * @inheritDoc
     */
    public static function createOrExisting(): ValidatorInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        } else {
            return new static();
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function __call(string $ruleName, array $arguments): ValidatorInterface
    {
        if(isset(static::$customs[$ruleName])) {
            /** @var Validatable $rule */
            $rule = clone static::$customs[$ruleName]->setArgs($arguments);
            $this->addRule($rule);
        } else {
            $this->addRule(Factory::getDefaultInstance()->rule($ruleName, $arguments));
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function check($input): void
    {
        try {
            parent::check($input);
        } catch (ValidationException $exception) {
            if (count($this->getRules()) == 1 && $this->template) {
                $exception->updateTemplate($this->template);
            }
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function setCustomRule(string $name, Validatable $rule): ValidatorInterface
    {
        static::$customs[$name] = $rule->setName($name);

        return $this;
    }
}