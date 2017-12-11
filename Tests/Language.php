<?php

namespace VM5\EntityTranslationsBundle\Tests;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Language
 * @package VM5\EntityTranslationsBundle\Tests
 * @ORM\Entity()
 */
class Language implements \VM5\EntityTranslationsBundle\Model\Language
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

    public function getLocale()
    {
        return $this->locale;
    }
}