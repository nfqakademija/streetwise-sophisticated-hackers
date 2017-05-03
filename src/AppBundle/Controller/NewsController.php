<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT a FROM AppBundle:News a ORDER BY a.date DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render(
            'news/index.html.twig',
            [
                'pagination' => $pagination,
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
