<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Twig\Extension;

use Arxy\EntityTranslationsBundle\Model\Translatable;
use Arxy\EntityTranslationsBundle\Model\Translation;
use Arxy\EntityTranslationsBundle\Translator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslationExtension extends AbstractExtension
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translationService)
    {
        $this->translator = $translationService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('translation', [$this, 'getTranslation']),
            new TwigFilter('translate', [$this, 'translate']),
        ];
    }

    public function getTranslation(Translatable $translatable, string $locale): ?Translation
    {
        return $this->translator->getTranslation($translatable, $locale);
    }

    public function translate(Translatable $translatable, string $field, string $locale = null): ?string
    {
        return $this->translator->translate($translatable, $field, $locale);
    }
}
