<?php

namespace VM5\EntityTranslationsBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VM5\EntityTranslationsBundle\Model\EditableTranslation;
use VM5\EntityTranslationsBundle\Model\Language;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Model\Translation;

class TranslationsType extends FormType
{

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * TranslationsType constructor.
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor = null,
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct($propertyAccessor);
        $this->managerRegistry = $managerRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['em'])) {
            $em = $this->managerRegistry->getManager($options['em']);
        } else {
            $em = $this->managerRegistry->getManager();
        }

        if ($em === null) {
            throw new \InvalidArgumentException('Entity manager not found for '.Language::class);
        }
        /** @var EntityRepository $repository */
        $repository = $em->getRepository(Language::class);

        if (isset($options['query_builder'])) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $options['query_builder']($repository);
            /** @var Language[] $languages */
            $languages = $queryBuilder->getQuery()->getResult();
        } else {
            /** @var Language[] $languages */
            $languages = $repository->findAll();
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
                        /** @var EditableTranslation $translation */
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
        $resolver->setDefined('em');
        $resolver->setDefined('query_builder');
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}