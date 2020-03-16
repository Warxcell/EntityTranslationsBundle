<?php

namespace Arxy\EntityTranslationsBundle\Tests\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Arxy\EntityTranslationsBundle\Tests\Entity\NewsTranslation;

class NewsTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class,
            array_merge(
                $options['title_options'],
                [
                    'required' => false,
                ]
            )
        );
        $builder->add(
            'description',
            TextareaType::class,
            array_merge(
                $options['description_options'],
                [
                    'required' => false,
                ]
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', NewsTranslation::class);
        $resolver->setDefault('title_options', []);
        $resolver->setDefault('description_options', []);
    }
}