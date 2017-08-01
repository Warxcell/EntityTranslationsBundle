# EntityTranslationsBundle

Very simple bundle that allows you to translate your entities.

Installation:
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
     * @ORM\OneToMany(targetEntity="NewsTranslations", mappedBy="translatable")
     */
    protected $translations;

    /**
     * @var NewsTranslations
     */
    private $currentTranslation;
    
    public function getTranslations() {
        return $this->translations;
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
