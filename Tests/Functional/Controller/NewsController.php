<?php

namespace VM5\EntityTranslationsBundle\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return new Response();
    }
}