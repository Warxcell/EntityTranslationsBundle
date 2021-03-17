<?php

declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\EventSubscriber;

use Arxy\EntityTranslationsBundle\Model\Translatable;
use Arxy\EntityTranslationsBundle\Translator;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;

class CurrentTranslationLoader implements EventSubscriber
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'postLoad',
            'postPersist',
            'onClear',
        );
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof Translatable) {
            $this->translator->initializeCurrentTranslation($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof Translatable) {
            $this->translator->initializeCurrentTranslation($entity);
        }
    }

    public function onClear(OnClearEventArgs $args)
    {
        $this->translator->clear();
    }
}
