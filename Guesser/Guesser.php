<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Guesser;


interface Guesser
{
    public function guessLocale(): ?string;

    public function guessFallbackLocales(): ?array;
}
