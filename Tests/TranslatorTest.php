<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests;

use Arxy\EntityTranslationsBundle\Tests\Entity\Language;
use Arxy\EntityTranslationsBundle\Tests\Entity\News;
use Arxy\EntityTranslationsBundle\Tests\Entity\NewsTranslation;
use Arxy\EntityTranslationsBundle\Translator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TranslatorTest extends TestCase
{
    public function testSingleLanguage()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('en');
        $loadedLocale = $translator->initializeCurrentTranslation($news);

        $this->assertEquals('en', $loadedLocale);

        $this->assertNotNull($news->getCurrentTranslation());
        $this->assertEquals($newsTranslationEnglish, $news->getCurrentTranslation());
        $this->assertEquals('This is title in english', $news->getCurrentTranslation()->getTitle());
    }

    public function testFallbackLanguage()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('fi', ['en', 'bg']);
        $loadedLocale = $translator->initializeCurrentTranslation($news);

        $this->assertEquals('en', $loadedLocale);

        $this->assertNotNull($news->getCurrentTranslation());
        $this->assertEquals($newsTranslationEnglish, $news->getCurrentTranslation());
        $this->assertEquals('This is title in english', $news->getCurrentTranslation()->getTitle());
    }

    public function testLanguageNotFound()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('fi');
        $loadedLocale = $translator->initializeCurrentTranslation($news);

        $this->assertNull($loadedLocale);

        $this->assertNull($news->getCurrentTranslation());
    }

    public function testChangeLanguage()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('en');
        $loadedLocale = $translator->initializeCurrentTranslation($news);

        $this->assertEquals('en', $loadedLocale);
        $this->assertNotNull($news->getCurrentTranslation());
        $this->assertEquals($newsTranslationEnglish, $news->getCurrentTranslation());
        $this->assertEquals('This is title in english', $news->getCurrentTranslation()->getTitle());


        $translator->setLocale('bg');

        $this->assertNotNull($news->getCurrentTranslation());
        $this->assertEquals($newsTranslationBulgarian, $news->getCurrentTranslation());
        $this->assertEquals('Това е заглавие на български', $news->getCurrentTranslation()->getTitle());
    }

    public function testSpecificLanguage()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('en');
        $loaded = $translator->initializeTranslation($news, 'fi');
        $this->assertFalse($loaded);

        $this->assertNull($news->getCurrentTranslation());
    }

    public function testChangeLanguageToNull()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('en');
        $loaded = $translator->initializeTranslation($news, 'en');
        $this->assertTrue($loaded);
        $this->assertNotNull($news->getCurrentTranslation());

        $loaded = $translator->initializeTranslation($news, 'fi');
        $this->assertFalse($loaded);
        $this->assertNull($news->getCurrentTranslation());
    }

    public function testDetach()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('en');
        $translator->initializeTranslation($news, 'en');
        $translator->detach($news);

        $translator->setLocale('bg');

        $this->assertNotNull($news->getCurrentTranslation());
        $this->assertEquals($newsTranslationEnglish, $news->getCurrentTranslation());
        $this->assertEquals('This is title in english', $news->getCurrentTranslation()->getTitle());
    }

    public function testGetTranslationIndependentlyOfCurrentTranslation()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('en');
        $loadedLocale = $translator->initializeCurrentTranslation($news);

        /** @var NewsTranslation $bgTranslation */
        $bgTranslation = $translator->getTranslation($news, 'bg');

        $fiTranslation = $translator->getTranslation($news, 'fi');


        $this->assertEquals('en', $loadedLocale);

        $this->assertNotNull($news->getCurrentTranslation());
        $this->assertEquals($newsTranslationEnglish, $news->getCurrentTranslation());
        $this->assertEquals('This is title in english', $news->getCurrentTranslation()->getTitle());

        $this->assertNotNull($bgTranslation);
        $this->assertEquals($newsTranslationBulgarian, $bgTranslation);
        $this->assertEquals('Това е заглавие на български', $bgTranslation->getTitle());

        $this->assertNull($fiTranslation);
    }

    public function testTranslateCurrentLocale()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('bg');
        $translator->setPropertyAccessor(PropertyAccess::createPropertyAccessor());

        $titleBg = $translator->translate($news, 'title');
        $this->assertEquals('Това е заглавие на български', $titleBg);
    }

    public function testTranslateNotCurrentLocale()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('bg');
        $translator->setPropertyAccessor(PropertyAccess::createPropertyAccessor());

        $titleFi = $translator->translate($news, 'title', 'fi');
        $this->assertNull($titleFi);
    }

    public function testTranslateNotExistingTranslation()
    {
        $englishLanguage = new Language('en');
        $bulgarianLanguage = new Language('bg');

        $newsTranslationEnglish = new NewsTranslation($englishLanguage, 'This is title in english');
        $newsTranslationBulgarian = new NewsTranslation($bulgarianLanguage, 'Това е заглавие на български');

        $news = new News(
            [
                $newsTranslationEnglish,
                $newsTranslationBulgarian,
            ]
        );

        $translator = new Translator('bg');
        $translator->setPropertyAccessor(PropertyAccess::createPropertyAccessor());

        $titleFi = $translator->translate($news, 'title', 'fi');
        $this->assertNull($titleFi);
    }
}