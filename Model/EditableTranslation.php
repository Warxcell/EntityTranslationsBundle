<?php

namespace VM5\EntityTranslationsBundle\Model;


interface EditableTranslation extends Translation
{
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