<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * News controller.
 *
 * @Route("news")
 */
class NewsController extends Controller
{
    /**
     * Lists all news entities.
     *
     * @Route("/", name="news_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository('AppBundle:News')->findAll();

        return $this->render(
            'news/index.html.twig',
            [
                'news' => $news,
            ]
        );
    }

    /**
     * Finds and displays a news entity.
     *
     * @Route("/{id}", name="news_show")
     * @Method("GET")
     */
    public function showAction(News $news)
    {

        return $this->render(
            'news/show.html.twig',
            [
                'news' => $news,
            ]
        );
    }
}
