<?php

namespace VM5\EntityTranslationsBundle\Model;

interface Translation
{
    /**
     * @return Language
     */
    public function getLanguage();
}
