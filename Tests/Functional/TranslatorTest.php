<?php

namespace VM5\EntityTranslationsBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use VM5\EntityTranslationsBundle\Translator;

class TranslatorTest extends WebTestCase
{
    private function buildDb($kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $application->run(
            new ArrayInput(
                array(
                    'doctrine:schema:create',
                )
            ),
            new NullOutput()
        );
    }

    public function testLoadLocaleFromSymfonyTranslator()
    {
        $client = static::createClient();

        $kernel = $client->getKernel();

        $container = $kernel->getContainer();

        /** @var Translator $translator */
        $translator = $container->get(Translator::class);

        $this->assertEquals('en', $translator->getLocale());

        $client->request(
            'GET',
            '/',
            [
                '_locale' => 'bg',
            ]
        );


        $this->assertEquals('bg', $translator->getLocale());
        $this->assertEquals(['en', 'bg'], $translator->getFallbackLocales());


    }
}