<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Language implements \Arxy\EntityTranslationsBundle\Model\Language
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column()
     */
    private $locale;

    /**
     * Language constructor.
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}