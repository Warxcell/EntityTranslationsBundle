<?php

namespace VM5\EntityTranslationsBundle\Model;

interface Translatable
{
    /**
     * @return Translation[]
     */
    public function getTranslations();

    public function setCurrentTranslation(Translation $translation);
}
