<?php
/**
 * Created by PhpStorm.
 * User: bozhidar.hristov
 * Date: 6.12.17
 * Time: 15:33
 */

namespace VM5\EntityTranslationsBundle\Tests\Guesser;


use VM5\EntityTranslationsBundle\Guesser\Guesser;

class StaticGuesser implements Guesser
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string[]|null
     */
    private $fallbackLocales;

    /**
     * StaticGuesser constructor.
     * @param $locale
     * @param $fallbackLocales
     */
    public function __construct($locale, $fallbackLocales = null)
    {
        $this->locale = $locale;
        $this->fallbackLocales = $fallbackLocales;
    }

    public function guessLocale()
    {
        return $this->locale;
    }

    public function guessFallbackLocales()
    {
        return $this->fallbackLocales;
    }
}