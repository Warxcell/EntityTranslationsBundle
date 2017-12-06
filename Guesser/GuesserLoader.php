<?php

namespace VM5\EntityTranslationsBundle\Guesser;

use VM5\EntityTranslationsBundle\Translator;

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

    /**
     * GuesserLoader constructor.
     * @param Translator $entityTranslator
     * @param Guesser[] $guessers
     */
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