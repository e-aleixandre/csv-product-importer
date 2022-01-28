<?php

namespace App\Entity\ValueObject;

use App\Exception\InvalidLanguageException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Language extends StringValueObject
{
    private const ES = 'es';
    private const FR = 'fr';
    private const EN = 'en';
    private const DE = 'de';

    public function __construct(string $language)
    {
        static::isValid($language);
        $this->value = $language;
    }

    public static function isValid(string $value): void
    {
        if (!in_array($value, self::allValues(), true))
        {
            throw new InvalidLanguageException();
        }
    }

    public static function es(): Language
    {
        return new self(self::ES);
    }

    public static function fr(): Language
    {
        return new self(self::FR);
    }

    public static function en(): Language
    {
        return new self(self::EN);
    }

    public static function de(): Language
    {
        return new self(self::DE);
    }
}
