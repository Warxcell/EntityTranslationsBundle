<?php

namespace VM5\EntityTranslationsBundle;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use VM5\EntityTranslationsBundle\Model\Language;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Model\Translation;

class Translator
{
    /**
     * @var Translatable[]
     */
    private $managed = [];

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string[]
     */
    private $fallbackLocales = [];

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor = null;

    /**
     * Translator constructor.
     * @param string $locale
     * @param array $fallbackLocales
     */
    public function __construct($locale, array $fallbackLocales = [])
    {
        $this->setLocale($locale);
        $this->fallbackLocales = $fallbackLocales;
    }

    public function setPropertyAccessor(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param Translatable $entity
     * @param Language|string $languageOrLocale
     * @return bool
     */
    public function initializeTranslation(Translatable $entity, $languageOrLocale)
    {
        $this->managed[$this->getId($entity)] = $entity;

        $translation = $this->getTranslation($entity, $languageOrLocale);
        $entity->setCurrentTranslation($translation);

        return $translation !== null;
    }

    /**
     * @param Translatable $entity
     * @param Language|string $languageOrLocale
     * @return Translation|null
     */
    public function getTranslation(Translatable $entity, $languageOrLocale)
    {
        $translations = $entity->getTranslations();

        foreach ($translations as $translation) {
            if ($this->checkLanguageOfEntity($translation, $languageOrLocale)) {
                return $translation;
            }
        }

        return null;
    }

    public function translate(Translatable $entity, $field, $locale = null)
    {
        if ($this->propertyAccessor === null) {
            throw new \LogicException('PropertyAccessor is required in order to use '.__METHOD__);
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }
        $translation = $this->getTranslation($entity, $locale);
        if ($translation) {
            return $this->propertyAccessor->getValue($translation, $field);
        }

        return null;
    }

    /**
     * @param Translation $translation
     * @param $languageOrLocale
     * @return bool
     */
    private function checkLanguageOfEntity(Translation $translation, $languageOrLocale)
    {
        return
            ($languageOrLocale instanceof Language && $translation->getLanguage() === $languageOrLocale)
            || $translation->getLanguage()->getLocale() == $languageOrLocale;
    }

    /**
     * @param Translatable $entity
     * @return null|string
     */
    public function initializeCurrentTranslation(Translatable $entity)
    {
        $currentLocale = $this->getLocale();
        if (!$this->initializeTranslation($entity, $currentLocale)) {
            $currentLocale = $this->initializeFallbackTranslation($entity);
        }

        return $currentLocale;
    }

    /**
     * @param $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        $this->flush();
    }

    /**
     * @param string[] $fallbackLocales
     */
    public function setFallbackLocales(array $fallbackLocales)
    {
        $this->fallbackLocales = $fallbackLocales;
    }

    /**
     * @param Translatable $translatable
     */
    public function detach(Translatable $translatable)
    {
        unset($this->managed[$this->getId($translatable)]);
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string[]
     */
    public function getFallbackLocales()
    {
        return $this->fallbackLocales;
    }

    private function flush()
    {
        foreach ($this->managed as $entity) {
            $this->initializeCurrentTranslation($entity);
        }
    }

    /**
     * @param Translatable $entity
     * @return string|null
     */
    private function initializeFallbackTranslation(Translatable $entity)
    {
        $fallbackLocales = $this->getFallbackLocales();
        foreach ($fallbackLocales as $fallback) {
            if ($this->initializeTranslation($entity, $fallback)) {
                return $fallback;
            }
        }

        return null;
    }

    /**
     * @param Translatable $translatable
     * @return string
     */
    private function getId(Translatable $translatable)
    {
        return spl_object_hash($translatable);
    }
}