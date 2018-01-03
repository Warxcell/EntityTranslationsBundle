<?php

namespace VM5\EntityTranslationsBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use VM5\EntityTranslationsBundle\Model\EditableTranslation;
use VM5\EntityTranslationsBundle\Model\Language as LanguageInterface;
use VM5\EntityTranslationsBundle\Model\Translatable;

/**
 * Class NewsTranslation
 * @package VM5\EntityTranslationsBundle\Tests
 * @ORM\Entity()
 */
class NewsTranslation implements EditableTranslation
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
     * @ORM\Column()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    private $description;

    /**
     * NewsTranslation constructor.
     * @param Language $language
     * @param string $title
     * @param $description
     */
    public function __construct(Language $language = null, $title = null, $description = null)
    {
        $this->language = $language;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return News
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * @param Translatable $translatable
     */
    public function setTranslatable(Translatable $translatable = null)
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

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param LanguageInterface $language
     * @return void
     */
    public function setLanguage(LanguageInterface $language)
    {
        $this->language = $language;
    }
}