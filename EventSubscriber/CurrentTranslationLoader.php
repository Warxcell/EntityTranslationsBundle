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
        return array('postLoad');
    }

    /**
     * @param LifecycleEventArgs $Event
     */
    public function postLoad(LifecycleEventArgs $Event)
    {
        $entity = $Event->getEntity();
        if (!$entity instanceof Translatable) {
            return;
        }

        $this->translator->initializeCurrentTranslation($entity);
    }
}
