<?php

namespace Arxy\EntityTranslationsBundle\Tests\Entity;

use Arxy\EntityTranslationsBundle\Model\Translatable;
use Arxy\EntityTranslationsBundle\Model\Translation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class News implements Translatable
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var NewsTranslation[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="NewsTranslation", mappedBy="translatable", cascade={"persist"}, orphanRemoval=true)
     */
    private $translations;

    /**
     * @var NewsTranslation
     */
    private $currentTranslation;

    /**
     * News constructor.
     * @param NewsTranslation[] $translations
     */
    public function __construct(array $translations = [])
    {
        $this->translations = new ArrayCollection($translations);
        foreach ($this->translations as $translation) {
            $this->addTranslation($translation);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function setCurrentTranslation(Translation $translation = null): void
    {
        $this->currentTranslation = $translation;
    }

    /**
     * @return NewsTranslation
     */
    public function getCurrentTranslation()
    {
        return $this->currentTranslation;
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    public function addTranslation(NewsTranslation $translation)
    {
        $this->getTranslations()->add($translation);
        $translation->setTranslatable($this);
    }

    public function removeTranslation(NewsTranslation $translation)
    {
        $this->getTranslations()->removeElement($translation);
        $translation->setTranslatable(null);
    }

    public function getTitle()
    {
        if ($this->currentTranslation !== null) {
            return $this->currentTranslation->getTitle();
        }

        return null;
    }

    public function getDescription()
    {
        if ($this->currentTranslation !== null) {
            return $this->currentTranslation->getDescription();
        }

        return null;
    }
}