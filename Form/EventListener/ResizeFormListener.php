<?php

declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Form\EventListener;

use Arxy\EntityTranslationsBundle\Model\EditableTranslation;
use Arxy\EntityTranslationsBundle\Model\Language;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
    private $languages;

    public function __construct(
        string $type,
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
            FormEvents::SUBMIT => 'submit',
        );
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var EditableTranslation[] $translations */
        $translations = $event->getData();

        $indexed = [];
        if (is_iterable($translations)) {
            foreach ($translations as $translation) {
                $indexed[$translation->getLanguage()->getLocale()] = $translation;
            }
        }
        $event->setData($indexed);

        foreach ($this->languages as $index => $language) {
            $locale = $language->getLocale();

            $options = $this->options;
            if (isset($this->languageOptions[$locale])) {
                $options = array_replace($options, $this->languageOptions[$locale]);
            }
            $options['language'] = $language;

            $form->add(
                $locale,
                $this->type,
                $options
            );
        }
    }

    public function submit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        foreach ($form->all() as $index => $child) {
            if ($child->isEmpty()) {
                unset($data[$index]);
            }
        }
        $event->setData($data);
    }
}
