<?php

namespace VM5\EntityTranslationsBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VM5\EntityTranslationsBundle\Model\Translatable;
use VM5\EntityTranslationsBundle\Translator;

class CurrentTranslationLoader implements EventSubscriber
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * CurrentTranslationLoader constructor.
     * @param Translator $translator
     */
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
        );
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
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
}
