<?php

namespace VM5\EntityTranslationsBundle\Twig\Extension;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Translator;

class TranslationExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * LanguageExtension constructor.
     * @param Translator $translationService
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(
        Translator $translationService,
        PropertyAccessor $propertyAccessor
    ) {
        $this->translator = $translationService;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('translation', [$this, 'getTranslation']),
            new \Twig_SimpleFilter('translate', [$this, 'translate']),
        ];
    }


    public function getTranslation(Translatable $translatable, $locale)
    {
        return $this->translator->getTranslation($translatable, $locale);
    }

    public function translate(Translatable $translatable, $locale, $field)
    {
        $translation = $this->translator->getTranslation($translatable, $locale);
        if ($translation) {
            return $this->propertyAccessor->getValue($translation, $field);
        }

        return null;
    }
}