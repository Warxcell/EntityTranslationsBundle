<?php

namespace VM5\EntityTranslationsBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use VM5\EntityTranslationsBundle\Tests\Entity\Language;
use VM5\EntityTranslationsBundle\Tests\Entity\News;
use VM5\EntityTranslationsBundle\Tests\Entity\NewsTranslation;
use VM5\EntityTranslationsBundle\Tests\Form\Type\NewsType;

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
     */
    private function insertLanguages(KernelInterface $kernel)
    {
        /** @var EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $languages = ['en', 'bg', 'fi'];

        foreach ($languages as $language) {
            $language = new Language($language);
            $em->persist($language);
        }

        $em->flush();
    }

    public function testFormWithEmptyRequiredEnglish()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $news = new News();

        $options['translation_options'] = [
            'entry_language_options' => [
                'en' => [
                    'required' => true,
                ],
            ],
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => '',
                    'description' => ''
                ],
                'bg' => [
                    'title' => '',
                    'description' => ''
                ],
                'fi' => [
                    'title' => 'Finnish title',
                    'description' => 'Finnish description'
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();
        }

        $errors = $form->getErrors(true);

        $this->assertEquals(1, $errors->count());

        foreach ($errors as $error) {
            $this->assertEquals('children[translations].children[en].data', $error->getCause()->getPropertyPath());
            $this->assertEquals('ad32d13f-c3d4-423b-909a-857b961eb720', $error->getCause()->getCode());
        }
    }

    public function testFormWithEmptyTitle()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $news = new News();

        $options['translation_options'] = [
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ],
                'title_options' => [
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => null,
                    'description' => 'English description'
                ],
                'bg' => [
                    'title' => 'Заглавие на български',
                    'description' => 'Съдържание на български'
                ],
                'fi' => [
                    'title' => 'Finnish title',
                    'description' => 'Finnish description'
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();
        }

        $errors = $form->getErrors(true);

        $this->assertEquals(1, $errors->count());

        foreach ($errors as $error) {
            $this->assertEquals('children[translations].children[en].children[title].data', $error->getCause()->getPropertyPath());
            $this->assertEquals('c1051bb4-d103-4f74-8988-acbcafc7fdc3', $error->getCause()->getCode());
        }
    }

    public function testSuccessInsertAllTranslations()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $news = new News();

        $options['translation_options'] = [
            'entry_language_options' => [
                'en' => [
                    'required' => true,
                ],
            ],
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ],
                'title_options' => [
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => 'English Title',
                    'description' => 'English description'
                ],
                'bg' => [
                    'title' => 'Заглавие на български',
                    'description' => 'Съдържание на български'
                ],
                'fi' => [
                    'title' => 'Finnish title',
                    'description' => 'Finnish description'
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();

            $this->assertNotEmpty($news->getId());
            $this->assertArrayHasKey('en', $news->getTranslations());
            $this->assertArrayHasKey('bg', $news->getTranslations());
            $this->assertArrayHasKey('fi', $news->getTranslations());
        }

        $errors = $form->getErrors(true);

        $this->assertEquals(0, $errors->count());
    }

    public function testSuccessInsertOneTranslations()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $news = new News();

        $options['translation_options'] = [
            'entry_language_options' => [
                'en' => [
                    'required' => true,
                ],
            ],
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ],
                'title_options' => [
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => 'English Title',
                    'description' => 'English description'
                ],
                'bg' => [
                    'title' => null,
                    'description' => null
                ],
                'fi' => [
                    'title' => null,
                    'description' => null
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();

            $this->assertNotEmpty($news->getId());
            $this->assertArrayHasKey('en', $news->getTranslations());
            $this->assertArrayNotHasKey('bg', $news->getTranslations());
            $this->assertArrayNotHasKey('fi', $news->getTranslations());
        }

        $errors = $form->getErrors(true);

        $this->assertEquals(0, $errors->count());
    }

    public function testDeleteTranslationsWithoutRequired()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $languageRepository = $em->getRepository(Language::class);

        $newsTranslationBg = new NewsTranslation($languageRepository->findOneBy(['locale' => 'bg']), 'Заглавие на български', 'Съдържание на български');
        $newsTranslationEn = new NewsTranslation($languageRepository->findOneBy(['locale' => 'en']), 'English title', 'English description');
        $newsTranslationFi = new NewsTranslation($languageRepository->findOneBy(['locale' => 'fi']), 'English title', 'English description');
        $news = new News([
            $newsTranslationBg,
            $newsTranslationEn,
            $newsTranslationFi,
        ]);

        $em->persist($news);
        $em->flush();

        $options['translation_options'] = [
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ],
                'title_options' => [
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => null,
                    'description' => null
                ],
                'bg' => [
                    'title' => null,
                    'description' => null
                ],
                'fi' => [
                    'title' => null,
                    'description' => null
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();

            $news = $em->getRepository(News::class)->find(1);

            $this->assertNotEmpty($news->getId());
            $this->assertEmpty($news->getTranslations());
        }

        $errors = $form->getErrors(true);
        $this->assertEquals(0, $errors->count());

    }

    public function testDeleteTranslationsWithRequired()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $languageRepository = $em->getRepository(Language::class);

        $newsTranslationBg = new NewsTranslation($languageRepository->findOneBy(['locale' => 'bg']), 'Заглавие на български', 'Съдържание на български');
        $newsTranslationEn = new NewsTranslation($languageRepository->findOneBy(['locale' => 'en']), 'English title', 'English description');
        $newsTranslationFi = new NewsTranslation($languageRepository->findOneBy(['locale' => 'fi']), 'English title', 'English description');
        $news = new News([
            $newsTranslationBg,
            $newsTranslationEn,
            $newsTranslationFi,
        ]);

        $em->persist($news);
        $em->flush();

        $options['translation_options'] = [
            'entry_language_options' => [
                'en' => [
                    'required' => true,
                ],
            ],
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ],
                'title_options' => [
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => null,
                    'description' => null
                ],
                'bg' => [
                    'title' => null,
                    'description' => null
                ],
                'fi' => [
                    'title' => null,
                    'description' => null
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();
        }

        $errors = $form->getErrors(true);
        $this->assertEquals(1, $errors->count());

        foreach ($errors as $error) {
            $this->assertEquals('children[translations].children[en].children[title].data', $error->getCause()->getPropertyPath());
            $this->assertEquals('c1051bb4-d103-4f74-8988-acbcafc7fdc3', $error->getCause()->getCode());
        }

    }

    public function testFieldWithValidationGroupAndEmptyValue()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $news = new News();

        $options['translation_options'] = [
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ],
                'description_options' => [
                    'constraints' => [
                        new NotBlank(['groups' => 'en'])
                    ],
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => null,
                    'description' => null
                ],
                'bg' => [
                    'title' => 'Заглавие на български',
                    'description' => null
                ],
                'fi' => [
                    'title' => 'Finnish Title',
                    'description' => null
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();
        }

        $errors = $form->getErrors(true);
        $this->assertEquals(0, $errors->count());
    }

    public function testFieldWithValidationGroupAndEmptyOnlyOneField()
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $container = $kernel->getContainer();

        $this->buildDb($kernel);
        $this->insertLanguages($kernel);
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $news = new News();

        $options['translation_options'] = [
            'entry_options' => [
                'constraints' => [
                    new NotNull(['groups' => 'en'])
                ],
                'description_options' => [
                    'constraints' => [
                        new NotBlank(['groups' => 'en'])
                    ],
                ]
            ]
        ];

        $data = [
            'translations' => [
                'en' => [
                    'title' => 'English Title',
                    'description' => null
                ],
                'bg' => [
                    'title' => 'Заглавие на български',
                    'description' => null
                ],
                'fi' => [
                    'title' => 'Finnish Title',
                    'description' => null
                ],
            ]
        ];

        $form = $container->get('form.factory')->create(NewsType::class, $news, $options);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();
        }

        $errors = $form->getErrors(true);
        foreach ($errors as $error) {
            $this->assertEquals('children[translations].children[en].children[description].data', $error->getCause()->getPropertyPath());
            $this->assertEquals('c1051bb4-d103-4f74-8988-acbcafc7fdc3', $error->getCause()->getCode());
        }

        $this->assertEquals(1, $errors->count());
    }
}