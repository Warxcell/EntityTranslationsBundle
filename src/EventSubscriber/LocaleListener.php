<?php

namespace VM5\EntityTranslationsBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use VM5\EntityTranslationsBundle\Translator as EntityTranslator;

class LocaleListener implements EventSubscriberInterface
{
    /**
     * @var EntityTranslator
     */
    private $entityTranslator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * LocaleListener constructor.
     * @param EntityTranslator $currentTranslationLoader
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityTranslator $currentTranslationLoader, TranslatorInterface $translator)
    {
        $this->entityTranslator = $currentTranslationLoader;
        $this->translator = $translator;
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
        $this->entityTranslator->setLocale($this->translator->getLocale());

        if ($this->translator instanceof Translator) {
            $this->entityTranslator->setFallbackLocales($this->translator->getFallbackLocales());
        }
    }
}