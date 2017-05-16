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
use AppBundle\Form\NewsType;

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
        $this->denyAccessUnlessGranted('list', $news);
        $em = $this->get('doctrine.orm.default_entity_manager');

        $user = $this->getUser();
        $userGroup = $user->getStudentGroup();

        if ($userGroup !== null) {
            $groupNews =
                $em
                    ->getRepository('AppBundle:News')
                    ->findGroupAndPublic($userGroup->getId());
        } elseif (!$user->isStudent()) {
            $groupNews =
                $em
                    ->getRepository('AppBundle:News')
                    ->findBy(
                        [],
                        ['date' => 'DESC']
                    );
        } else {
            $groupNews =
                $em
                    ->getRepository('AppBundle:News')
                    ->findPublic();
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $groupNews,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render(
            'news/index.html.twig',
            [
                'pagination' => $pagination,
                'news' => $news,
            ]
        );
    }

    /**
     * Finds and displays a news entity.
     *
     * @Route("/{id}", name="news_show", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param News $news
     * @param Request $request
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

    /**
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
