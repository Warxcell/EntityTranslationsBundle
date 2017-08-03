<?php

namespace VM5\EntityTranslationsBundle\Model;

interface Translatable
{
    /**
     * @return Translation[]
     */
    public function getTranslations();

    /**
     * @param Translation $translation
     * @return mixed
     */
    public function setCurrentTranslation(Translation $translation);
}
