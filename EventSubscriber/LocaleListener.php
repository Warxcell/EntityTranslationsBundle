<?php

namespace VM5\EntityTranslationsBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use VM5\EntityTranslationsBundle\Guesser\Guesser;
use VM5\EntityTranslationsBundle\Translator as EntityTranslator;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var EntityTranslator
     */
    private $entityTranslator;

    /**
     * @var Guesser[]
     */
    private $guessers = [];

    /**
     * LocaleListener constructor.
     * @param EntityTranslator $entityTranslator
     * @param Guesser[] $guessers
     */
    public function __construct(EntityTranslator $entityTranslator, array $guessers)
    {
        $this->entityTranslator = $entityTranslator;
        $this->guessers = $guessers;
    }

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
        foreach ($this->guessers as $guesser) {
            $locale = $guesser->guessLocale();
            $this->entityTranslator->setLocale($locale);

            $fallbackLocales = $guesser->guessFallbackLocales();
            if ($fallbackLocales !== null) {
                $this->entityTranslator->setFallbackLocales($fallbackLocales);
            }
        }
    }
}