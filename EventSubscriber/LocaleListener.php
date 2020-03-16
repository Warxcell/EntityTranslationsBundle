<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\EventSubscriber;

use Arxy\EntityTranslationsBundle\Guesser\GuesserLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var GuesserLoader
     */
    private $guessLoader;

    public function __construct(GuesserLoader $guessLoader)
    {
        $this->guessLoader = $guessLoader;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->guessLoader->load();
    }
}
