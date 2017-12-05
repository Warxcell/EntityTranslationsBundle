<?php

namespace VM5\EntityTranslationsBundle\Tests;


use VM5\EntityTranslationsBundle\Model\Translation;

class NewsTranslation implements Translation
{
    /**
     * @var Language
     */
    private $language;

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