<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests\Functional;

use Arxy\EntityTranslationsBundle\Model\Translation;
use Arxy\EntityTranslationsBundle\Tests\Entity\Language;
use Arxy\EntityTranslationsBundle\Tests\Entity\News;
use Arxy\EntityTranslationsBundle\Tests\Entity\NewsTranslation;
use Arxy\EntityTranslationsBundle\Tests\Form\Type\NewsType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class FormTest extends WebTestCase
{
    /**
     * @param KernelInterface $kernel
     */
    private function buildDb(KernelInterface $kernel)
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

    /**
     * @param KernelInterface $kernel
     * @return Language[]
     */
    private function insertLanguages(KernelInterface $kernel)
    {
        /** @var EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $languages = [
            'en' => new Language('en'),
            'bg' => new Language('bg'),
            'fi' => new Language('fi'),
        ];

        foreach ($languages as $language) {
            $em->persist($language);
        }

        $em->flush();

        return $languages;
    }

    /**
     * @param Translation[] $translations
     * @param $locale
     * @return boolean
     */
    private function containsTranslation($translations, string $locale): bool
    {
        foreach ($translations as $translation) {
            if ($translation->getLanguage()->getLocale() == $locale) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Translation[] $translations
     * @param $locale
     */
    private function assertHasTranslation($translations, $locale)
    {
        $this->assertTrue(
            $this->containsTranslation($translations, $locale),
            sprintf('Does not contains %s translation', $locale)
        );
    }

    public function testSuccessInsertAllTranslations()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);

        $news = new News();

        $data = [
            'translations' => [
                'en' => [
                    'title' => 'English Title',
                    'description' => 'English description',
                ],
                'bg' => [
                    'title' => 'Заглавие на български',
                    'description' => 'Съдържание на български',
                ],
                'fi' => [
                    'title' => 'Finnish title',
                    'description' => 'Finnish description',
                ],
            ],
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news);
        $form->submit($data);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertCount(3, $news->getTranslations());
        $this->assertHasTranslation($news->getTranslations(), 'en');
        $this->assertHasTranslation($news->getTranslations(), 'bg');
        $this->assertHasTranslation($news->getTranslations(), 'fi');
    }

    public function testDeleteEmptyTranslations()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $languages = $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $newsTranslationBg = new NewsTranslation(
            $languages['bg'],
            'Заглавие на български',
            'Съдържание на български'
        );
        $newsTranslationEn = new NewsTranslation(
            $languages['en'],
            'English title',
            'English description'
        );
        $newsTranslationFi = new NewsTranslation(
            $languages['fi'],
            'English title',
            'English description'
        );
        $news = new News(
            [
                $newsTranslationBg,
                $newsTranslationEn,
                $newsTranslationFi,
            ]
        );

        $em->persist($news);
        $em->flush();

        $form = $container->get('form.factory')->create(NewsType::class, $news);
        $form->submit(
            [
                'translations' => [
                    'en' => [
                        'title' => null,
                        'description' => null,
                    ],
                    'bg' => [
                        'title' => null,
                        'description' => null,
                    ],
                    'fi' => [
                        'title' => null,
                        'description' => null,
                    ],
                ],
            ]
        );

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertEmpty($news->getTranslations());
    }

    public function testWithEmptyData()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);

        $news = new News();

        $form = $container->get('form.factory')->create(NewsType::class, $news);
        $form->submit(
            [
                'translations' => [
                    'en' => [
                        'title' => 'English Title',
                        'description' => 'Description EN',
                    ],
                ],
            ]
        );

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());

        $this->assertCount(1, $news->getTranslations());
        $this->assertHasTranslation($news->getTranslations(), 'en');
    }

    //public function testDataConstraintsMapping()
    //{
    //    $client = static::createClient();
    //    $kernel = $client->getKernel();
    //    $container = $kernel->getContainer();
    //
    //    $this->buildDb($kernel);
    //    $this->insertLanguages($kernel);
    //
    //    $news = new News();
    //
    //    $form = $container->get('form.factory')->create(NewsType::class, $news);
    //    $form->submit(
    //        [
    //            'translations' => [
    //                'en' => [
    //                    'title' => null,
    //                    'description' => 'Description EN',
    //                ],
    //                'bg' => [
    //                    'title' => null,
    //                    'description' => 'Description BG',
    //                ],
    //            ],
    //        ]
    //    );
    //
    //    $this->assertTrue($form->isSubmitted());
    //    $this->assertFalse($form->isValid());
    //    $this->assertCount(2, $form->getErrors(true, true));
    //    $this->assertCount(1, $form->get('translations')->get('en')->get('title')->getErrors());
    //    $this->assertCount(1, $form->get('translations')->get('bg')->get('title')->getErrors());
    //}
}
