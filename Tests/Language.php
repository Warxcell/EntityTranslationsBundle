<?php
/**
 * Created by PhpStorm.
 * User: bozhidar.hristov
 * Date: 5.12.17
 * Time: 11:12
 */

namespace VM5\EntityTranslationsBundle\Tests;


class Language implements \VM5\EntityTranslationsBundle\Model\Language
{
    /**
     * @var string
     */
    private $locale;

    /**
     * Language constructor.
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}