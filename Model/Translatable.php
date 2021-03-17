<?php

declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Model;

interface Translatable
{
    /**
     * @return Translation[]
     */
    public function getTranslations();

    /**
     * @param Translation $translation
     * @return void
     */
    public function setCurrentTranslation(Translation $translation = null): void;
}
