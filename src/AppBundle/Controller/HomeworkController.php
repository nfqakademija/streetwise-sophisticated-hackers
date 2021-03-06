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
use AppBundle\Form\HomeworkType;

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

        $em = $this->get('doctrine.orm.default_entity_manager');

        $user = $this->getUser();
        $userGroup = $user->getStudentGroup();

        if ($userGroup !== null) {
            $groups =
                $em
                    ->getRepository('AppBundle:StudentGroup')
                    ->findBy(
                        ['id' => $userGroup]
                    );
        } elseif (!$user->isStudent()) {
            $groups =
                $em
                    ->getRepository('AppBundle:StudentGroup')
                    ->findBy(
                        [],
                        ['id' => 'DESC']
                    );
        } else {
            $groups = [];
        }

        return $this->render(
            'homework/index.html.twig',
            [
                'groups' => $groups,
                'homework' => $homework,
            ]
        );
    }

    /**
     * Finds and displays a homework entity.
     *
     * @Route("/{id}", name="homework_show", requirements={"id": "\d+"})
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

        $em = $this->get('doctrine.orm.default_entity_manager');

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
                'assignment'=> $assignment,
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
        $homework = new Homework();
        $this->denyAccessUnlessGranted('new', $homework);
        $homework->setLecturer($this->getUser());
        $homework->setDueDate(new \DateTime('tomorrow 23:59'));
        $form = $this->createForm(HomeworkType::class, $homework);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $homework = $form->getData();
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($homework);
            $em->flush();
            return $this->redirectToRoute('homework_show', [
                'id' => $homework->getId(),
            ]);
        }
        return $this->render(
            '@AppBundle/views/homework/create.html.twig',
            ['homework_form' => $form->createView()]
        );
    }
}
