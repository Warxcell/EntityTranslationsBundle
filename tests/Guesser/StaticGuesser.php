<?php

declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests\Guesser;

use Arxy\EntityTranslationsBundle\Guesser\Guesser;

class StaticGuesser implements Guesser
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string[]|null
     */
    private $fallbackLocales;

    /**
     * StaticGuesser constructor.
     * @param $locale
     * @param $fallbackLocales
     */
    public function __construct($locale = null, $fallbackLocales = null)
    {
        $this->locale = $locale;
        $this->fallbackLocales = $fallbackLocales;
    }

    public function guessLocale(): ?string
    {
        return $this->locale;
    }

    public function guessFallbackLocales(): ?array
    {
        return $this->fallbackLocales;
    }
}
