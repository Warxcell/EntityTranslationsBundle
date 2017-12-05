<?php

namespace VM5\EntityTranslationsBundle\Model;


interface EditableTranslation extends Translation
{
    /**
     * @param Translatable $translatable
     * @return void
     */
    public function setTranslatable(Translatable $translatable);

    /**
     * @param Language $language
     * @return void
     */
    public function setLanguage(Language $language);
}