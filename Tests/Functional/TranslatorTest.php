<?php

namespace VM5\EntityTranslationsBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use VM5\EntityTranslationsBundle\Tests\Language;
use VM5\EntityTranslationsBundle\Tests\News;
use VM5\EntityTranslationsBundle\Tests\NewsTranslation;
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
            new ConsoleOutput()
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
        $this->assertEquals(['en', 'fi'], $translator->getFallbackLocales());
    }

    public function testOrmEntityTranslationLoaded()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $this->buildDb($kernel);

        $container = $kernel->getContainer();

        $em = $container->get('doctrine')->getManager();

        $englishLanguage = new Language('en');
        $em->persist($englishLanguage);


        $bulgarianLanguage = new Language('bg');
        $em->persist($bulgarianLanguage);
        $em->flush();

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $em->persist($news);
        $em->flush();


        /** @var Translator $translator */
        $translator = $container->get(Translator::class);

        $this->assertEquals('en', $translator->getLocale());

        $client->request(
            'GET',
            sprintf('/news/%s', $news->getId()),
            [
                '_locale' => 'bg',
            ]
        );

        $response = $client->getResponse();

        $this->assertEquals('bg', $translator->getLocale());
        $this->assertEquals(['en', 'bg'], $translator->getFallbackLocales());
    }
}