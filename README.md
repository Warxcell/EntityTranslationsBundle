# EntityTranslationsBundle

Very simple bundle that allows you to translate your entities.

## Installation:
- composer require vm5/entity-translations-bundle
- Register bundle in AppKernel.php: `new VM5\EntityTranslationsBundle\VM5EntityTranslationsBundle()`
- Translatable must `implements \VM5\EntityTranslationsBundle\Model\Translatable`
- Translations must `implements \VM5\EntityTranslationsBundle\Model\Translation`
- You must have 1 entity containing all the languages, it must `implements \VM5\EntityTranslationsBundle\Language`
- Include services.xml in config.yml: 
```
imports:
    - { resource: "@VM5EntityTranslationsBundle/Resources/config/services.xml" }
```

No configuration is needed. Current and fallback locales are taken from Symfony:

<a href="http://symfony.com/doc/current/translation.html#configuration" target="_blank">Symfony Translations</a>    
<a href="https://symfony.com/doc/current/translation/locale.html" target="_blank">How to Work with the User's Locale</a>

```yaml
framework:
    translator:      { fallbacks: ["bg", "de"] }
```


Example entities:

Language
```php
<?php

namespace Example;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="languages")
 */
class Language implements \VM5\EntityTranslationsBundle\Model\Language
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="locale", type="string", length=5)
     */
    protected $locale;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Language
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
```

News.php

```php
<?php

namespace Example;

use Doctrine\ORM\Mapping as ORM;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Model\Translation;

/**
 * @ORM\Entity()
 * @ORM\Table(name="news")
 */
class News implements Translatable
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;


    /**
     * @ORM\OneToMany(targetEntity="NewsTranslation", mappedBy="translatable", cascade={"ALL"}, orphanRemoval=true)
     */
    protected $translations;

    /**
     * @var NewsTranslation
     */
    private $currentTranslation;
    
    public function getTranslations() {
        return $this->translations;
    }
    
    public function addTranslation(NewsTranslation $translation) {
        $this->getTranslations()->add($translation);
        $translation->setTranslatable($this);
    }

    public function setCurrentTranslation(Translation $translation) {
        $this->currentTranslation = $translation;
    }
    
    public function getTitle() {
        if($this->currentTranslation) {
            return $this->currentTranslation->getTitle();
        }
    }
}
```

NewsTranslations.php
```php
<?php

namespace Example;

use Doctrine\ORM\Mapping as ORM;
use VM5\EntityTranslationsBundle\Model\Translation;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_translations")
 */
class NewsTranslation implements Translation
{

    /**
     * @var News
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="News", inversedBy="translations")
     */
    protected $translatable;

    /**
     * @var Language
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Language")
     */
    protected $language;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @return News
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * @param News $translatable
     */
    public function setTranslatable(News $translatable)
    {
        $this->translatable = $translatable;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->name = $title;
    }
}
```

Then you can translate them on yourself

```php
$news = new News();

$englishTranslation = new NewsTranslation();
$englishTranslation->setLanguage($englishLanguage);
$englishTranslation->setTitle('Title on english');
$news->addTranslation($englishTranslation);

$em->persist($news);
$em->flush();
```

## Internal API:

If you wish to change language of all managed entities:

```php
$this->get('vm5_entity_translations.translator')->setLocale('bg');
```

You can change language of single entity:
```php
$initializedLocale = $this->get('vm5_entity_translations.translator')->initializeTranslation($entity, 'bg');
```
`$initializedLocale` is actual locale initialized in entity - it's not necessary to be `bg`, it could be one of fallback locales. 
Argument #2 can be either string locale or Language entity.

You can detach entity from manager
```php
$this->get('vm5_entity_translations.translator')->detach($entity);
```

So it won't be affected by locale changing.


If you wish to get single translation without initialize it, you can use:

```php
/** @var $translation \VM5\EntityTranslationBundle\Model\Translation */
$translation = $this->get('vm5_entity_translations.translator')->getTranslation($entity, 'bg');
```

Argument #2 can be either string locale or Language entity.

You can also use embedded Twig filters to translate in twig:

```twig
{{ news|translate('en', 'title')|lower }}
```
or get the whole translation:
```twig
{% set translation = news|translation('en') %}
{% if translation %}
  {{ translation.title }}
{% endif %}
```

## Using form to easily translate entities.

```yaml
doctrine:
   orm:
        # search for the "ResolveTargetEntityListener" class for an article about this
        resolve_target_entities: 
            VM5\EntityTranslationsBundle\Model\Language: Example\Language
```
Translation should implements `EditableTranslation` instead of simple `Translation`

```php
use VM5\EntityTranslationsBundle\Model\EditableTranslation;

class NewsTranslation implements EditableTranslation
```

And then you can:

```php
->add(
    'translations',
    \VM5\EntityTranslationsBundle\Form\Type\TranslationsType::class,
    [
        'entry_type' => NewsTranslationType::class,
        'em' => 'manager_name',
        'query_builder' => function(EntityRepository $repo) {
            return $repo->createQueryBuilder('languages');
        }
    ]
)
```