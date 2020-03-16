<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Model;

interface Translation
{
    public function getLanguage(): Language;
}
