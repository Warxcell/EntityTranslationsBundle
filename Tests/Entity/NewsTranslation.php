<?php

namespace VM5\EntityTranslationsBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use VM5\EntityTranslationsBundle\Model\Translation;

/**
 * Class NewsTranslation
 * @package VM5\EntityTranslationsBundle\Tests
 * @ORM\Entity()
 */
class NewsTranslation implements Translation
{
    /**
     * @var Language
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumn(referencedColumnName="locale")
     */
    private $language;

    /**
     * @var News
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="News", inversedBy="translations")
     */
    private $translatable;

    /**
     * @var string
     */
    private $title;

    /**
     * NewsTranslation constructor.
     * @param Language $language
     * @param string $title
     */
    public function __construct(Language $language, $title)
    {
        $this->language = $language;
        $this->title = $title;
    }

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
    public function setTranslatable($translatable)
    {
        $this->translatable = $translatable;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }
}