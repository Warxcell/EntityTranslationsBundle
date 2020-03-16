<?php

namespace Arxy\EntityTranslationsBundle\Twig\Extension;

use Arxy\EntityTranslationsBundle\Model\Translatable;
use Arxy\EntityTranslationsBundle\Model\Translation;
use Arxy\EntityTranslationsBundle\Translator;

class TranslationExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translationService)
    {
        $this->translator = $translationService;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('translation', [$this, 'getTranslation']),
            new \Twig_SimpleFilter('translate', [$this, 'translate']),
        ];
    }

    public function getTranslation(Translatable $translatable, string $locale): ?Translation
    {
        return $this->translator->getTranslation($translatable, $locale);
    }

    public function translate(Translatable $translatable, string $field, string $locale = null): ?string
    {
        return $this->translator->translate($translatable, $field, $locale);
    }
}
