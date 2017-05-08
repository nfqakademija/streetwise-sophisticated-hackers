<?php

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Admin\CommentTrait;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use AppBundle\Form\NewsType;

/**
 * News controller.
 *
 * @Route("news")
 */
class NewsController extends Controller
{
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
     * @Route("/{id}", name="news_show", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param News $news
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, News $news)
    {
        $this->denyAccessUnlessGranted('show', $news);

        $comments = $this->getEntityComments($news);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->denyAccessUnlessGranted('new', $comment);
            $comment->setAuthor($this->getUser());
            $comment = $commentForm->getData();
            $thread = $this->getThreadPromise($news);
            $comment->setThread($thread);
            $thread->addComment($comment);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute(
                'news_show',
                [
                    'id' => $news->getId()
                ]
            );
        }

        return $this->render(
            'news/show.html.twig',
            [
                'news' => $news,
                'comments' => $comments,
                'comment_form' => $commentForm->createView()
            ]
        );
    }

    /**
     * @Route("/create", name="news_create")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $news = new News();
        $this->denyAccessUnlessGranted('new', $news);
        $news->setAuthor($this->getUser());
        $news->setDate(new \DateTime());
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news = $form->getData();
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($news);
            $em->flush();
            return $this->redirectToRoute('news_show', [
                'id' => $news->getId(),
            ]);
        }
        return $this->render(
            '@AppBundle/views/news/create.html.twig',
            ['news_form' => $form->createView()]
        );
    }
}
