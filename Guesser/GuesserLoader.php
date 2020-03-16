<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Guesser;

use Arxy\EntityTranslationsBundle\Translator;

class GuesserLoader
{
    /**
     * @var Translator
     */
    private $entityTranslator;

    /**
     * @var Guesser[]
     */
    private $guessers = [];

    public function __construct(Translator $entityTranslator, array $guessers)
    {
        $this->entityTranslator = $entityTranslator;
        $this->guessers = $guessers;
    }

    public function load()
    {
        $localeLoaded = false;
        $fallbackLocalesLoaded = false;

        foreach ($this->guessers as $guesser) {
            $locale = $guesser->guessLocale();
            if ($localeLoaded === false && $locale !== null) {
                $this->entityTranslator->setLocale($locale);
                $localeLoaded = true;
            }

            $fallbackLocales = $guesser->guessFallbackLocales();
            if ($fallbackLocalesLoaded === false && $fallbackLocales !== null) {
                $this->entityTranslator->setFallbackLocales($fallbackLocales);
                $fallbackLocalesLoaded = true;
            }
        }
    }
}
