<?php

namespace VM5\EntityTranslationsBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VM5\EntityTranslationsBundle\Form\EventListener\ResizeFormListener;
use VM5\EntityTranslationsBundle\Model\Language;

class TranslationsType extends AbstractType
{

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * TranslationsType constructor.
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
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
        $resizeListener = new ResizeFormListener(
            $options['entry_type'],
            $options['entry_options'],
            $options['entry_language_options'],
            $languages
        );

        $builder->addEventSubscriber($resizeListener);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('em');
        $resolver->setDefined('query_builder');
        $resolver->setRequired('entry_type');

        $options = [];
        $options['entry_options'] = [];
        $options['entry_language_options'] = [];
        $options['by_reference'] = false;

        $resolver->setDefaults($options);
    }
}
