<?php

namespace VM5\EntityTranslationsBundle\Model;

interface Translation
{
    /**
     * @return Language
     */
    public function getLanguage();

    /**
     * @param Translatable $translatable
     * @return mixed
     */
    public function setTranslatable(Translatable $translatable);

    /**
     * @param Language $language
     * @return mixed
     */
    public function setLanguage(Language $language);
}
