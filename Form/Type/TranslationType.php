<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Form\Type;

use Arxy\EntityTranslationsBundle\Model\EditableTranslation;
use Arxy\EntityTranslationsBundle\Model\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                if ($form->isRequired()) {
                    return;
                }

                foreach ($form->all() as $child) {
                    if (!$child->isEmpty()) {
                        return;
                    }
                }

                $event->setData(null);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault(
            'validation_groups',
            function (FormInterface $form) {
                return ['Default', $form->getName()];
            }
        );
        $resolver->setDefault('required', false);
        $resolver->setRequired('language');
        $resolver->setRequired('data_class');
        $resolver->setAllowedTypes('language', Language::class);
        $resolver->setDefault(
            'label',
            function (Options $options) {
                return \Locale::getDisplayName($options['language']->getLocale());
            }
        );
        $resolver->setDefault('error_bubbling', false);
        $resolver->setDefault(
            'empty_data',
            function (Options $options) {
                return function (FormInterface $form, $viwData) use ($options) {
                    $dataClass = $form->getConfig()->getOption('data_class');

                    /** @var EditableTranslation $data */
                    $data = new $dataClass;
                    $data->setLanguage($options['language']);

                    return $data;
                };
            }
        );
    }
}
