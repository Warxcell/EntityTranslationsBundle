<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle;

use Arxy\EntityTranslationsBundle\Model\Language;
use Arxy\EntityTranslationsBundle\Model\Translatable;
use Arxy\EntityTranslationsBundle\Model\Translation;
use Symfony\Component\PropertyAccess\PropertyAccessor;

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

    public function __construct(string $locale, array $fallbackLocales = [])
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
    public function initializeTranslation(Translatable $entity, $languageOrLocale): bool
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
    public function getTranslation(Translatable $entity, $languageOrLocale): ?Translation
    {
        $translations = $entity->getTranslations();

        foreach ($translations as $translation) {
            if ($this->checkLanguageOfEntity($translation, $languageOrLocale)) {
                return $translation;
            }
        }

        return null;
    }

    public function translate(Translatable $entity, $field, $locale = null): ?string
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
    private function checkLanguageOfEntity(Translation $translation, $languageOrLocale): bool
    {
        return
            ($languageOrLocale instanceof Language && $translation->getLanguage() === $languageOrLocale)
            || $translation->getLanguage()->getLocale() == $languageOrLocale;
    }

    public function initializeCurrentTranslation(Translatable $entity): ?string
    {
        $currentLocale = $this->getLocale();
        if (!$this->initializeTranslation($entity, $currentLocale)) {
            $currentLocale = $this->initializeFallbackTranslation($entity);
        }

        return $currentLocale;
    }

    public function setLocale(string $locale)
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

    public function detach(Translatable $translatable): void
    {
        unset($this->managed[$this->getId($translatable)]);
    }

    public function clear(): void
    {
        $this->managed = [];
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string[]
     */
    public function getFallbackLocales(): array
    {
        return $this->fallbackLocales;
    }

    private function flush(): void
    {
        foreach ($this->managed as $entity) {
            $this->initializeCurrentTranslation($entity);
        }
    }

    private function initializeFallbackTranslation(Translatable $entity): ?string
    {
        $fallbackLocales = $this->getFallbackLocales();
        foreach ($fallbackLocales as $fallback) {
            if ($this->initializeTranslation($entity, $fallback)) {
                return $fallback;
            }
        }

        return null;
    }

    private function getId(Translatable $translatable): int
    {
        return spl_object_id($translatable);
    }
}
