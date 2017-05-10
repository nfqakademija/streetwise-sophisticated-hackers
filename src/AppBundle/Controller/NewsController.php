<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CommentTrait;
use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Form\CommentType;
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
     * Trait with comment methods
     */
    use CommentTrait;

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
     * @Method({"GET", "POST"})
     *
     * @param News $news
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(News $news, Request $request)
    {
        $this->denyAccessUnlessGranted('show', $news);

        $query = $this->getEntityComments($news);

        $paginator  = $this->get('knp_paginator');
        $comments = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->denyAccessUnlessGranted('new', $comment);

            $comment->setAuthor($this->getUser());
            $comment = $commentForm->getData();
            $thread = $this->getThreadPromise($news);
            $comment->setThread($thread);

            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute(
                'news_show',
                [
                    'id' => $news->getId(),
                ]
            );
        }

        return $this->render(
            'news/show.html.twig',
            [
                'news' => $news,
                'comment_form' => $commentForm->createView(),
                'comments' => $comments,
            ]
        );
    }
}
