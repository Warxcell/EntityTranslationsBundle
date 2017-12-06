<?php

namespace VM5\EntityTranslationsBundle\Tests\Guesser;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use VM5\EntityTranslationsBundle\Guesser\SymfonyTranslationGuesser;

class TranslatorTest extends TestCase
{
    public function testLocale()
    {
        $symfonyTranslator = new Translator('bg');

        $guesser = new SymfonyTranslationGuesser($symfonyTranslator);

        $this->assertEquals('bg', $guesser->guessLocale());
    }

    public function testFallbackLocales()
    {
        $fallbackLocales = ['bg', 'en', 'fi'];

        $symfonyTranslator = new Translator('bg');
        $symfonyTranslator->setFallbackLocales($fallbackLocales);

        $guesser = new SymfonyTranslationGuesser($symfonyTranslator);

        $this->assertEquals($fallbackLocales, $guesser->guessFallbackLocales());
    }
}