<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Model;

interface Language
{
    public function getLocale(): string;
}
