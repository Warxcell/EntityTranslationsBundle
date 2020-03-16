<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Guesser;

use Symfony\Contracts\Translation\TranslatorInterface;

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

    public function guessLocale(): ?string
    {
        return $this->symfonyTranslator->getLocale();
    }

    public function guessFallbackLocales(): ?array
    {
        if (method_exists($this->symfonyTranslator, 'getFallbackLocales')) {
            return $this->symfonyTranslator->getFallbackLocales();
        }

        return null;
    }
}
