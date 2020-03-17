# EntityTranslationsBundle

[![Build Status](https://travis-ci.org/Warxcell/EntityTranslationsBundle.png)](https://travis-ci.org/arxy/EntityTranslationsBundle)

[![codecov](https://codecov.io/gh/Warxcell/EntityTranslationsBundle/branch/master/graph/badge.svg)](https://codecov.io/gh/arxy/EntityTranslationsBundle)

Very simple bundle that allows you to translate your entities.

## Installation: 
###### it is recommented to install X.Y.* version - This project follow <a target="_blank" href="https://semver.org/">semver</a> - Patch versions will be always compatible with each other. Minor versions may contain minor BC-breaks.
- composer require arxy/entity-translations-bundle
- Register bundle in AppKernel.php: `new Arxy\EntityTranslationsBundle\ArxyEntityTranslationsBundle()`
- Translatable must `implements \Arxy\EntityTranslationsBundle\Model\Translatable`
- Translations must `implements \Arxy\EntityTranslationsBundle\Model\Translation`
- You must have 1 entity containing all the languages, it must `implements \Arxy\EntityTranslationsBundle\Language`
- Include services.xml in config.yml: 
```
imports:
    - { resource: "@ArxyEntityTranslationsBundle/Resources/config/services.xml" }
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
class Language implements \Arxy\EntityTranslationsBundle\Model\Language
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
    public function getLocale(): string
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
use Arxy\EntityTranslationsBundle\Model\Translatable;
use Arxy\EntityTranslationsBundle\Model\Translation;

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
    
    public function getTranslations() 
    {
        return $this->translations;
    }
    
    /**
     * This is important, as form has default option: by_reference = false
     * so here we set the mapped side entity. 
     * @param NewsTranslation|null $translation
     */
    public function addTranslation(NewsTranslation $translation) 
    {
        $this->getTranslations()->add($translation);
        $translation->setTranslatable($this);
    }
    
    /**
     * This is also used by form.
     * @param NewsTranslation|null $translation
     */
    public function removeTranslation(NewsTranslation $translation)
    {
        $this->getTranslations()->removeElement($translation);
    }

    /**
    * This method is used by bundle to inject current translation.
    */
    public function setCurrentTranslation(Translation $translation = null): void
    {
        $this->currentTranslation = $translation;
    }
    
    /**
     * @return string|null 
     */
    public function getTitle()
    {
        return !$this->currentTranslation ?: $this->currentTranslation->getTitle();
    }
}
```

NewsTranslations.php
```php
<?php

namespace Example;

use Arxy\EntityTranslationsBundle\Model\Language;use Doctrine\ORM\Mapping as ORM;
use Arxy\EntityTranslationsBundle\Model\Translation;

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
    public function setTranslatable(News $translatable = null)
    {
        $this->translatable = $translatable;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
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
        $this->title = $title;
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
$this->get('arxy.entity_translations.translator')->setLocale('bg');
```

You can change language of single entity:
```php
$initializedLocale = $this->get('arxy.entity_translations.translator')->initializeTranslation($entity, 'bg');
```
`$initializedLocale` is actual locale initialized in entity - it's not necessary to be `bg`, it could be one of fallback locales. 
Argument #2 can be either string locale or Language entity.

You can detach entity from manager
```php
$this->get('arxy.entity_translations.translator')->detach($entity);
```

So it won't be affected by locale changing.


If you wish to get single translation without initialize it, you can use:

```php
/** @var $translation \Arxy\EntityTranslationBundle\Model\Translation */
$translation = $this->get('arxy_entity_translations.translator')->getTranslation($entity, 'bg');
```

Argument #2 can be either string locale or Language entity.

You can also use translator to translate objects instead of using setCurrentTranslation.
```php
$translation = $this->get('arxy_entity_translations.translator')->translate($entity, 'field', 'bg');
```
Argument #3 is optional. If omitted current locale is assumed.


You can also use class instead of key for accessing service:
```php
... $this->get(\Arxy\EntityTranslationsBundle\Translator::class) ...
```
You can also use embedded Twig filters to translate in twig:

```twig
{{ news|translate('title')|lower }}
{{ news|translate('title', 'en')|lower }}
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
            Arxy\EntityTranslationsBundle\Model\Language: Example\Language
```

Translatable should have `addTranslation`, `removeTranslation` (
see <a href="https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference" target="_blank">by-reference</a>
and
<a href="https://symfony.com/doc/current/doctrine/associations.html" target="_blank">How to Work with Doctrine Associations / Relations</a>
):

```php

    public function addTranslation(NewsTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setTranslatable($this);
        }
    }


    public function removeTranslation(NewsTranslation $translation)
    {
        $this->translations->removeElement($translation);
        $translation->setTranslatable(null);
    }
```
Translation should implements `EditableTranslation` instead of simple `Translation`

```php
use Arxy\EntityTranslationsBundle\Model\EditableTranslation;

class NewsTranslation implements EditableTranslation
```

Load form theme (optionally)
```yaml
twig:
    form_themes:
        - 'ArxyEntityTranslationsBundle::bootstrap_3_tab_layout.html.twig'
```

Use `'ArxyEntityTranslationsBundle::bootstrap_4_tab_layout.html.twig'` for Bootstrap 4 support.


You need to create translation's form.
```php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new SomeBulgarianSymbolConstraint([
                        'groups'=> ['bg']
                    ]) // This will be validated only on bg locale
                    new SomeChineseSymbolConstraint([
                        'groups'=> ['zh']
                    ])  // This will be validated only on zh locale
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', NewsTranslation::class); // this is important
        $resolver->setDefault('constraints', [
            new NotNull(
                [
                    'groups' => ['en'], // make only english required
                ]
            ),
        ]);
    }
}

```

And then you can:

```php
->add(
    'translations',
    \Arxy\EntityTranslationsBundle\Form\Type\TranslationsType::class,
    [
        'entry_type' => NewsTranslationType::class,
        'em' => 'manager_name', // optional
        'query_builder' => function(EntityRepository $repo) {
            return $repo->createQueryBuilder('languages');
        }, // optional
        'entry_language_options' => [
            'en' => [
                'required' => true,
            ]
        ],
    ]
)
```

in your main form.

It's important to include `required` in `entry_language_options` for specific locales, because validation is triggered 
only when language is not empty or it's required. 

Language is assumed as not empty when at least one of the fields are filled in.
