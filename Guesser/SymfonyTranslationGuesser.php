<?php

namespace VM5\EntityTranslationsBundle\Guesser;


use Symfony\Component\Translation\TranslatorInterface;

class SymfonyTranslationGuesser implements Guesser
{
    /**
     * @var TranslatorInterface
     */
    private $symfonyTranslator;

    /**
     * SymfonyTranslationGuesser constructor.
     * @param TranslatorInterface $symfonyTranslator
     */
    public function __construct(TranslatorInterface $symfonyTranslator)
    {
        $this->symfonyTranslator = $symfonyTranslator;
    }

    public function guessLocale()
    {
        return $this->symfonyTranslator->getLocale();
    }

    public function guessFallbackLocales()
    {
        if (method_exists($this->symfonyTranslator, 'getFallbackLocales')) {
            return $this->symfonyTranslator->getFallbackLocales();
        }

        return null;
    }
}
