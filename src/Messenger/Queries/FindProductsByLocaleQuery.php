<?php

namespace App\Messenger\Queries;

use App\Entity\ValueObject\Language;

class FindProductsByLocaleQuery
{
    private Language $locale;

    public function __construct(Language $locale)
    {
        $this->locale = $locale;
    }

    public function locale(): Language
    {
        return new Language($this->locale);
    }
}