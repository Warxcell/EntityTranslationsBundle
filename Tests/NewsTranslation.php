<?php
/**
 * Created by PhpStorm.
 * User: bozhidar.hristov
 * Date: 5.12.17
 * Time: 11:10
 */

namespace VM5\EntityTranslationsBundle\Tests;


use VM5\EntityTranslationsBundle\Model\Translation;

class NewsTranslation implements Translation
{
    /**
     * @var Language
     */
    private $language;

    private $title;

    /**
     * NewsTranslation constructor.
     * @param Language $language
     * @param $title
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