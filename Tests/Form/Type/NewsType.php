<?php

declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests\Form\Type;

use Arxy\EntityTranslationsBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'translations',
            TranslationsType::class,
            array_merge(
                $options['translation_options'],
                [
                    'entry_type' => NewsTranslationType::class,
                ]
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation_options', []);
    }
}
