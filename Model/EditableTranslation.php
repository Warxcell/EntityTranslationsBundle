<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Model;

interface EditableTranslation extends Translation
{
    public function setLanguage(Language $language): void;
}
