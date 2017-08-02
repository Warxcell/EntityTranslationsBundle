<?php

namespace VM5\EntityTranslationsBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VM5\EntityTranslationsBundle\Model\Language;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Model\Translation;

class CurrentTranslationLoader implements EventSubscriber
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

    public function __construct($locale = 'en', array $fallbackLocales = [])
    {
        $this->setLocale($locale);
        $this->fallbackLocales = $fallbackLocales;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array('postLoad');
    }

    /**
     * @param LifecycleEventArgs $Event
     */
    public function postLoad(LifecycleEventArgs $Event)
    {
        $entity = $Event->getEntity();
        if (!$entity instanceof Translatable) {
            return;
        }

        $this->initializeCurrentTranslation($entity);
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

    public function setFallbackLocales($fallbackLocales)
    {
        $this->fallbackLocales = $fallbackLocales;
    }

    public function detach(Translatable $translatable)
    {
        unset($this->managed[$this->getId($translatable)]);
    }

    public function getLocale()
    {
        return $this->locale;
    }

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

    private function initializeFallbackTranslation(Translatable $entity)
    {
        $fallbackLocales = $this->getFallbackLocales();
        foreach ($fallbackLocales as $fallback) {
            if ($this->initializeTranslation($entity, $fallback)) {
                return $fallback;
            }
        }
    }

    private function getId(Translatable $translatable)
    {
        return spl_object_hash($translatable);
    }
}
