<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CommentTrait;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Lecture;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\LectureType;

/**
 * Lecture controller.
 *
 * @Route("lecture")
 */
class LectureController extends Controller
{
    /**
     * Trait with comment methods
     */
    use CommentTrait;

    /**
     * Lists all lecture entities.
     *
     * @Route("/", name="lecture_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $lecture = new Lecture();
        $this->denyAccessUnlessGranted('list', $lecture);

        $em = $this->get('doctrine.orm.default_entity_manager');

        $lectures =
            $em
                ->getRepository('AppBundle:Lecture')
                ->findBy(
                    [],
                    ['date' => 'ASC']
                );

        return $this->render(
            'lecture/index.html.twig',
            [
                'lectures' => $lectures,
            ]
        );
    }

    /**
     * Finds and displays a lecture entity.
     *
     * @Route("/{id}", name="lecture_show", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param Lecture $lecture
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Lecture $lecture, Request $request)
    {
        $this->denyAccessUnlessGranted('show', $lecture);

        $query = $this->getEntityComments($lecture);

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
            $thread = $this->getThreadPromise($lecture);
            $comment->setThread($thread);

            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute(
                'lecture_show',
                [
                    'id' => $lecture->getId(),
                ]
            );
        }
        return $this->render(
            'lecture/show.html.twig',
            [
                'lecture' => $lecture,
                'comment_form' => $commentForm->createView(),
                'comments' => $comments,
            ]
        );
    }

    /**
     * @Route("/create", name="lecture_create")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $lecture = new Lecture();
        $this->denyAccessUnlessGranted('new', $lecture);
        $lecture->setLecturer($this->getUser());
        $lecture->setDate(new \DateTime('tomorrow 17:00'));
        $form = $this->createForm(LectureType::class, $lecture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lecture = $form->getData();
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($lecture);
            $em->flush();
            return $this->redirectToRoute('lecture_show', [
                'id' => $lecture->getId(),
            ]);
        }
        return $this->render(
            '@AppBundle/views/lecture/create.html.twig',
            ['lecture_form' => $form->createView()]
        );
    }
}
