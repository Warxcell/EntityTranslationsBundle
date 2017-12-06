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
        foreach ($this->guessers as $guesser) {
            $locale = $guesser->guessLocale();
            $this->entityTranslator->setLocale($locale);

            $fallbackLocales = $guesser->guessFallbackLocales();
            if ($fallbackLocales !== null) {
                $this->entityTranslator->setFallbackLocales($fallbackLocales);
            }
        }
    }
}