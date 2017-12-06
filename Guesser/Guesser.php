<?php

namespace VM5\EntityTranslationsBundle\Guesser;


interface Guesser
{
    /**
     * @return string
     */
    public function guessLocale();

    /**
     * @return string[]|null
     */
    public function guessFallbackLocales();
}