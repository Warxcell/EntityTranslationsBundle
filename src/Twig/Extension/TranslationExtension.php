<?php

namespace VM5\EntityTranslationsBundle\Twig\Extension;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use VM5\EntityTranslationsBundle\EventSubscriber\CurrentTranslationLoader;
use VM5\EntityTranslationsBundle\Model\Translatable;

class LanguageExtension extends \Twig_Extension
{
    /**
     * @var CurrentTranslationLoader
     */
    private $translationService;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * LanguageExtension constructor.
     * @param CurrentTranslationLoader $translationService
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(
        CurrentTranslationLoader $translationService,
        PropertyAccessor $propertyAccessor
    ) {
        $this->translationService = $translationService;
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
        return $this->translationService->getTranslation($translatable, $locale);
    }

    public function translate(Translatable $translatable, $locale, $field)
    {
        $translation = $this->translationService->getTranslation($translatable, $locale);
        if ($translation) {
            return $this->propertyAccessor->getValue($translation, $field);
        }

        return null;
    }
}