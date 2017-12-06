<?php

namespace VM5\EntityTranslationsBundle\Tests\Guesser;

use PHPUnit\Framework\TestCase;
use VM5\EntityTranslationsBundle\Guesser\GuesserLoader;

class GuesserLoaderTest extends TestCase
{
    public function testLocaleLoad()
    {
        $translator = new \VM5\EntityTranslationsBundle\Translator('en');

        $guesserLoader = new GuesserLoader(
            $translator, [
                new StaticGuesser('fi', []),
            ]
        );

        $guesserLoader->load();

        $this->assertEquals('fi', $translator->getLocale());
    }

    public function testFallbackLocalesLoad()
    {
        $translator = new \VM5\EntityTranslationsBundle\Translator('en');

        $fallbacks = ['fi', 'en', 'bg'];

        $guesserLoader = new GuesserLoader(
            $translator, [
                new StaticGuesser('fi', $fallbacks),
            ]
        );

        $guesserLoader->load();

        $this->assertEquals($fallbacks, $translator->getFallbackLocales());
    }

    public function testNullFallbackLocalesLoad()
    {
        $translator = new \VM5\EntityTranslationsBundle\Translator('en');

        $guesserLoader = new GuesserLoader(
            $translator, [
                new StaticGuesser('fi'),
            ]
        );

        $guesserLoader->load();

        $this->assertCount(0, $translator->getFallbackLocales());
    }

}