<?php

namespace VM5\EntityTranslationsBundle;

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
     * Translator constructor.
     * @param string $locale
     * @param array $fallbackLocales
     */
    public function __construct($locale = 'en', array $fallbackLocales = [])
    {
        $this->setLocale($locale);
        $this->fallbackLocales = $fallbackLocales;
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
        if (!$translation) {
            return false;
        }
        $entity->setCurrentTranslation($translation);

        return true;
    }

    /**
     * @param Translatable $entity
     * @param Language|string $languageOrLocale
     * @return Translation|null
     */
    public function getTranslation(Translatable $entity, $languageOrLocale)
    {
        $translations = $entity->getTranslations();

        $translation = $translations->filter(
            function (Translation $item) use ($languageOrLocale) {
                $translationLanguage = $item->getLanguage();
                if ($languageOrLocale instanceof Language) {
                    return $translationLanguage === $languageOrLocale;
                } else {
                    return $translationLanguage->getLocale() === $languageOrLocale;
                }
            }
        )->first();
        if ($translation === false) {
            return null;

        }

        return $translation;
    }

    public function initializeCurrentTranslation(Translatable $entity)
    {
        $currentLocale = $this->getLocale();
        $success = $this->initializeTranslation($entity, $currentLocale);

        if ($success == false) {
            $currentLocale = $this->initializeFallbackTranslation($entity);
        }

        return $currentLocale;
    }

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