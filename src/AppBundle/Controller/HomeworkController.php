<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CommentTrait;
use AppBundle\Entity\Assignment;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Homework;
use AppBundle\Form\AssignmentType;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Homework controller.
 *
 * @Route("homework")
 */
class HomeworkController extends Controller
{
    /**
     * Trait with comment methods
     */
    use CommentTrait;

    /**
     * Lists all homework entities.
     *
     * @Route("/", name="homework_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $homework = new Homework();
        $this->denyAccessUnlessGranted('list', $homework);

        $em = $this->getDoctrine()->getManager();

        $homeworks =
            $em
                ->getRepository('AppBundle:Homework')
                ->findBy(
                    [],
                    ['dueDate' => 'ASC']
                );

        return $this->render(
            'homework/index.html.twig',
            [
                'homeworks' => $homeworks,
            ]
        );
    }

    /**
     * Finds and displays a homework entity.
     *
     * @Route("/{id}", name="homework_show")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Homework $homework
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Homework $homework)
    {
        $this->denyAccessUnlessGranted('show', $homework);

        $assignments = $homework->getAssignments();

        $em = $this->getDoctrine()->getManager();

        $myAssignment = $em->getRepository('AppBundle:Assignment')
            ->findOneBy(
                [
                    'student' => $this->getUser()->getId(),
                    'homework' => $homework->getId()
                ]
            );

        $assignment = new Assignment();
        $assignment->setDate(new \DateTime());
        $assignment->setHomework($homework);
        $assignment->setStudent($this->getUser());

        $assignmentForm = $this->createForm(AssignmentType::class, $assignment);

        $assignmentForm->handleRequest($request);

        if ($assignmentForm->isSubmitted() && $assignmentForm->isValid() && $myAssignment == null) {
            $this->denyAccessUnlessGranted('new', $assignment);
            $assignment = $assignmentForm->getData();
            $assignment->setDate(new \DateTime());

            $em->persist($assignment);
            $em->flush();

            return $this->redirectToRoute(
                'homework_show',
                [
                    'id' => $homework->getId(),
                ]
            );
        }

        $query = $this->getEntityComments($homework);

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
            $thread = $this->getThreadPromise($homework);
            $comment->setThread($thread);

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute(
                'homework_show',
                [
                    'id' => $homework->getId(),
                ]
            );
        }

        return $this->render(
            'homework/show.html.twig',
            [
                'homework' => $homework,
                'assignment_form' => $assignmentForm->createView(),
                'my_assignment' => $myAssignment,
                'assignments' => $assignments,
                'comment_form' => $commentForm->createView(),
                'comments' => $comments,
            ]
        );
    }
}
