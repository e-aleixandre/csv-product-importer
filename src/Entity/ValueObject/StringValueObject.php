<?php

namespace App\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;

abstract class StringValueObject
{
    /**
     * @ORM\Column(type="string")
     */
    protected string $value;

    public static function allValues(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }

    abstract public static function isValid(string $value);

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
