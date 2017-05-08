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
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $news = new News();
        $this->denyAccessUnlessGranted('show', $news);
        $em = $this->getDoctrine()->getManager();
        $query = $em
            ->getRepository('AppBundle:News')
            ->findBy(
                [],
                ['date' => 'DESC']
            );

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
     *
     * @param News $news
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(News $news)
    {
        $this->denyAccessUnlessGranted('show', $news);
        return $this->render(
            'news/show.html.twig',
            [
                'news' => $news,
            ]
        );
    }
}
