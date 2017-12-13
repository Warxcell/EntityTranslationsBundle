<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 11.12.2017 г.
 * Time: 14:49
 */

namespace VM5\EntityTranslationsBundle\Tests\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VM5\EntityTranslationsBundle\Form\Type\TranslationsType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'translations',
            TranslationsType::class,
            array_merge($options['translation_options'],
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