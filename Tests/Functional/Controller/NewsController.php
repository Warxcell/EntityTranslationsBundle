<?php
declare(strict_types=1);

namespace Arxy\EntityTranslationsBundle\Tests\Functional\Controller;

use Arxy\EntityTranslationsBundle\Tests\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class NewsController
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * NewsController constructor.
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
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