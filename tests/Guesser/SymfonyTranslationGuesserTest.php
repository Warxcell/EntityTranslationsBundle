<?php

declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests\Guesser;

use Arxy\EntityTranslationsBundle\Guesser\SymfonyTranslationGuesser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Translator;

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

    public function testTranslatorWithoutFallbackLocales()
    {
        $identityTranslator = new IdentityTranslator();
        $identityTranslator->setLocale('bg');

        $guesser = new SymfonyTranslationGuesser($identityTranslator);
        $this->assertNull($guesser->guessFallbackLocales());
    }
}
