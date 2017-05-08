<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assignment;
use AppBundle\Entity\Homework;
use AppBundle\Form\AssignmentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\HomeworkType;
use AppBundle\Controller\Admin\CommentTrait;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

/**
 * Homework controller.
 *
 * @Route("homework")
 */
class HomeworkController extends Controller
{
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

        $em = $this->get('doctrine.orm.default_entity_manager');

        $comments = $this->getEntityComments($homework);
        $assignments = $homework->getAssignments();
        $myAssignment = $em->getRepository('AppBundle:Assignment')
            ->findOneBy(
                [
                    'student' => $this->getUser()->getId(),
                    'homework' => $homework->getId()
                ]
            );

        $comment = new Comment();
        $commentForm = $this->get('form.factory')
            ->createNamedBuilder('commentForm', CommentType::class, $comment)
            ->getForm();

        $assignment = new Assignment();
        $assignment->setDate(new \DateTime());
        $assignment->setHomework($homework);
        $assignment->setStudent($this->getUser());
        $assignmentForm = $this->get('form.factory')
            ->createNamedBuilder('assignmentForm', AssignmentType::class, $assignment)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            if ($request->request->has('assignmentForm')) {
                $assignmentForm->handleRequest($request);
                if ($assignmentForm->isSubmitted() && $assignmentForm->isValid() && $myAssignment == null) {
                    $assignment = new Assignment();
                    $this->denyAccessUnlessGranted('new', $assignment);
                    $assignment = $assignmentForm->getData();
                    $em->persist($assignment);
                    $em->flush();

                    return $this->redirectToRoute('homework_show', [
                        'id' => $homework->getId(),
                    ]);
                }
            }
            if ($request->request->has('commentForm')) {
                $commentForm->handleRequest($request);
                if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                    $this->denyAccessUnlessGranted('new', $comment);
                    $comment = $commentForm->getData();
                    $comment->setAuthor($this->getUser());

                    $thread = $this->getThreadPromise($homework);
                    $comment->setThread($thread);
                    $thread->addComment($comment);
                    $em->persist($comment);
                    $em->flush();

                    return $this->redirectToRoute(
                        'homework_show',
                        [
                            'id' => $homework->getId()
                        ]
                    );
                }
            }
        }

        return $this->render(
            'homework/show.html.twig',
            [
                'homework' => $homework,
                'assignment_form' => $assignmentForm->createView(),
                'comment_form' => $commentForm->createView(),
                'my_assignment' => $myAssignment,
                'assignments' => $assignments,
                'comments' => $comments,
            ]
        );
    }

    /**
     * @Route("/create", name="homework_create")
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
