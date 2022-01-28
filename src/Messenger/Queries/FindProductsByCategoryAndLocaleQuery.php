<?php

namespace App\Messenger\Queries;

use App\Entity\ValueObject\Language;

class FindProductsByCategoryAndLocaleQuery
{
    private string $category;
    private Language $locale;

    public function __construct(string $category, Language $locale)
    {
        $this->category = $category;
        $this->locale = $locale;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function locale(): Language
    {
        return new Language($this->locale);
    }
}