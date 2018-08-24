<?php

namespace VM5\EntityTranslationsBundle\Guesser;


interface Guesser
{
    /**
     * @return string|null
     */
    public function guessLocale();

    /**
     * @return string[]|null
     */
    public function guessFallbackLocales();
}
