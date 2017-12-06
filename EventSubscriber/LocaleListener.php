<?php

namespace VM5\EntityTranslationsBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use VM5\EntityTranslationsBundle\Guesser\GuesserLoader;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var GuesserLoader
     */
    private $guessLoader;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->guessLoader->load();
    }
}