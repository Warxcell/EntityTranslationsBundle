<?php

namespace VM5\EntityTranslationsBundle\Tests\Functional\Controller;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use VM5\EntityTranslationsBundle\Tests\Entity\News;

class NewsController
{
    /**
     * @var AbstractManagerRegistry
     */
    private $doctrine;

    /**
     * NewsController constructor.
     * @param AbstractManagerRegistry $doctrine
     */
    public function __construct(AbstractManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function indexAction()
    {
        return new Response();
    }

    public function newsReadAction($id)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->doctrine->getManager();

        /** @var News $news */
        $news = $em->find(News::class, $id);

        return new Response($news->getTitle());
    }
}