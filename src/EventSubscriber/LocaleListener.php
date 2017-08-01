<?php

namespace VM5\EntityTranslationsBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var CurrentTranslationLoader
     */
    private $currentTranslationLoader;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(CurrentTranslationLoader $currentTranslationLoader, TranslatorInterface $translator)
    {
        $this->currentTranslationLoader = $currentTranslationLoader;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->currentTranslationLoader->setLocale($this->translator->getLocale());

        if ($this->translator instanceof Translator) {
            $this->currentTranslationLoader->setFallbackLocales($this->translator->getFallbackLocales());
        }
    }
}