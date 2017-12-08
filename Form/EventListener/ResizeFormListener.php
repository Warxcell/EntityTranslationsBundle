<?php

namespace VM5\EntityTranslationsBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use VM5\EntityTranslationsBundle\Model\EditableTranslation;
use VM5\EntityTranslationsBundle\Model\Language;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Model\Translation;

class ResizeFormListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $languageOptions;

    /**
     * @var Language[]
     */
    private $languages = [];

    /**
     * @var array
     */
    private $forDelete = [];

    /**
     * ResizeFormListener constructor.
     * @param string $type
     * @param array $options
     * @param array $languageOptions
     * @param Language[] $languages
     */
    public function __construct(
        $type,
        array $options,
        array $languageOptions,
        array $languages
    ) {
        $this->type = $type;
        $this->options = $options;
        $this->languageOptions = $languageOptions;
        $this->languages = $languages;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
        );
    }

    private function getOptions(Language $language)
    {
        $options = $this->options;
        $options['validation_groups'] = function (FormInterface $form) {
            if (($form->isEmpty() || isset($this->forDelete[$form->getName()])) && !$form->isRequired()) {
                return false;
            } else {
                return ['Default', $form->getName()];
            }
        };
        $options['required'] = false;
        $options['label'] = \Locale::getDisplayName($language->getLocale());

        if (isset($this->languageOptions[$language->getLocale()])) {
            $options = array_replace($options, $this->languageOptions[$language->getLocale()]);
        }

        $options['property_path'] = '['.$language->getLocale().']';
        $options['empty_data'] = function (FormInterface $form) {
            if ($form->isEmpty()) {
                return null;
            } else {
                $dataClass = $form->getConfig()->getOption('data_class');

                return new $dataClass;
            }
        };
        $options['error_bubbling'] = false;

        return $options;
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        foreach ($this->languages as $value) {
            if (!$form->has($value->getLocale())) {
                $form->add(
                    $value->getLocale(),
                    $this->type,
                    $this->getOptions($value)
                );
            }
        }

        /** @var Translation[] $translations */
        $translations = $event->getData();

        $newData = [];
        foreach ($translations as $translation) {
            $newData[$translation->getLanguage()->getLocale()] = $translation;
        }

        $event->setData($newData);
    }

    public function preSubmit(FormEvent $event)
    {
        $translations = $event->getData();

        $isEmpty = function ($data) use (&$isEmpty) {
            if (is_array($data)) {
                foreach ($data as $each) {
                    if (!$isEmpty($each)) {
                        return false;
                    }
                }

                return true;
            }

            return empty($data);
        };

        foreach ($this->languages as $language) {
            $translation = $translations[$language->getLocale()];
            if ($isEmpty($translation)) {
                $this->forDelete[$language->getLocale()] = $language->getLocale();
            }
        }
    }

    public function submit(FormEvent $event)
    {
        /** @var EditableTranslation[] $translations */
        $translations = $event->getData();

        /** @var Translatable $translatable */
        $translatable = $event->getForm()->getParent()->getData();

        $forDelete = [];
        foreach ($this->languages as $language) {
            $translation = $translations[$language->getLocale()];
            if ($translation === null) {
                $forDelete[] = $language->getLocale();
            } else {
                $translation = $translations[$language->getLocale()];
                $translation->setTranslatable($translatable);
                $translation->setLanguage($language);
            }
        }
        foreach ($forDelete as $item) {
            unset($translations[$item]);
        }
        foreach ($this->forDelete as $item) {
            unset($translations[$item]);
        }

        $event->setData($translations);
    }
}
