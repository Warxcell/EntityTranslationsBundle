<?php

namespace VM5\EntityTranslationsBundle\Model;


interface EditableTranslation extends Translation
{
    /**
     * @param Language $language
     * @return void
     */
    public function setLanguage(Language $language);
}
