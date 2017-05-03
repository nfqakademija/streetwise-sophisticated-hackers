<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assignment;
use AppBundle\Entity\Homework;
use AppBundle\Form\AssignmentType;
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
     * Lists all homework entities.
     *
     * @Route("/", name="homework_index")
     * @Method("GET")
     */
    public function indexAction()
    {
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
     */
    public function showAction(Request $request, Homework $homework)
    {
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

            return $this->redirectToRoute('homework_show', [
                'id' => $homework->getId(),
            ]);
        }

        return $this->render(
            'homework/show.html.twig',
            [
                'homework' => $homework,
                'assignment_form' => $assignmentForm->createView(),
                'my_assignment' => $myAssignment,
                'assignments' => $assignments,
            ]
        );
    }
}
