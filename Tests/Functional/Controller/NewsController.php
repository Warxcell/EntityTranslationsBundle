<?php

namespace VM5\EntityTranslationsBundle\Tests\Functional\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use VM5\EntityTranslationsBundle\Tests\Entity\News;

class NewsController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return new Response();
    }

    /**
     * @Route("/news/{id}", name="news_read")
     */
    public function newsReadAction($id)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine')->getManager();

        /** @var News $news */
        $news = $em->find(News::class, $id);

        return new Response($news->getTitle());
    }
}