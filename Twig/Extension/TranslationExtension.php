<?php

namespace VM5\EntityTranslationsBundle\Twig\Extension;

use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Translator;

class TranslationExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * LanguageExtension constructor.
     * @param Translator $translationService
     */
    public function __construct(Translator $translationService)
    {
        $this->translator = $translationService;
    }

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('translation', [$this, 'getTranslation']),
            new \Twig_SimpleFilter('translate', [$this, 'translate']),
        ];
    }

    /**
     * @param Translatable $translatable
     * @param string $locale
     * @return null|\VM5\EntityTranslationsBundle\Model\Translation
     */
    public function getTranslation(Translatable $translatable, $locale)
    {
        return $this->translator->getTranslation($translatable, $locale);
    }

    /**
     * @param Translatable $translatable
     * @param string $locale
     * @param string $field
     * @return null|string
     */
    public function translate(Translatable $translatable, $field, $locale = null)
    {
        return $this->translator->translate($translatable, $field, $locale);
    }
}
