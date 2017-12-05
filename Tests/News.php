<?php

namespace VM5\EntityTranslationsBundle\Tests;

use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Model\Translation;

class News implements Translatable
{
    /**
     * @var NewsTranslation[]
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
    public function __construct(array $translations)
    {
        $this->translations = $translations;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function setCurrentTranslation(Translation $translation = null)
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
}