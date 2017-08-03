<?php

namespace App\TranslationBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VM5\EntityTranslationsBundle\Model\Language;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Model\Translation;

class TranslationsType extends FormType
{
    /**
     * @var Language[]
     */
    private $languages = [];

    /**
     * @param Language[] $languages
     */
    public function setLanguages(array $languages)
    {
        $this->languages = $languages;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $this->languages;

        if (isset($options['languages'])) {
            $languages = $options['languages'];
        } else {
            if (isset($options['locales'])) {
                $locales = $options['locales'];
                $languages = array_filter(
                    $languages,
                    function (Language $language) use ($locales) {
                        return in_array($language->getLocale(), $locales);
                    }
                );
            }
        }

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($builder, $options, $languages) {
                /** @var Translatable $translatable */
                $translatable = $event->getForm()->getParent()->getData();

                /** @var Translation[] $translations */
                $translations = $event->getData();

                $newTranslations = [];
                foreach ($translations as $translation) {
                    $newTranslations[$translation->getLanguage()->getLocale()] = $translation;
                }

                $prototype = $builder->create(uniqid(), $options['entry_type'], $options['entry_options']);
                $translationClass = $prototype->getForm()->getConfig()->getOption('data_class');


                foreach ($languages as $language) {
                    $locale = $language->getLocale();
                    if (!isset($newTranslations[$locale])) {
                        /** @var Translation $translation */
                        $translation = new $translationClass;
                        $translation->setLanguage($language);
                        $translation->setTranslatable($translatable);
                        $newTranslations[$locale] = $translation;
                    }
                }

                $event->setData($newTranslations);
            },
            1024
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                $isEmpty = function ($data) {
                    foreach ($data as $value) {
                        if (!empty($value)) {
                            return false;
                        }
                    }

                    return true;
                };
                foreach ($data as $language => $values) {
                    if ($isEmpty($values)) {
                        unset($data[$language]);
                        $event->getForm()->remove($language);
                    }
                }

                $event->setData($data);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('delete_empty', true);
        $resolver->setDefault('allow_delete', true);
        $resolver->setDefined('locales');
        $resolver->setDefined('languages');
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}