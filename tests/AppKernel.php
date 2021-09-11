<?php

declare(strict_types=1);

use Arxy\EntityTranslationsBundle\ArxyEntityTranslationsBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [];
        $bundles[] = new FrameworkBundle();
        $bundles[] = new DoctrineBundle();
        $bundles[] = new ArxyEntityTranslationsBundle();

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }
}
