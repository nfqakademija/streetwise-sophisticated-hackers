<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assignment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\GradeType;

/**
 * Assignment controller.
 *
 * @Route("assignment")
 */
class AssignmentController extends Controller
{
    /**
     * Finds and displays a assignment entity.
     *
     * @Route("/{id}", name="assignment_show")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Assignment $assignment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Assignment $assignment)
    {

        $this->denyAccessUnlessGranted('show', $assignment);

        $gradeForm = $this->createForm(GradeType::class, $assignment);
        $gradeForm->handleRequest($request);
        if ($gradeForm->isSubmitted() && $gradeForm->isValid()) {
            $this->denyAccessUnlessGranted('grade', $assignment);
            $assignment = $gradeForm->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($assignment);
            $em->flush();
            return $this->redirectToRoute('assignment_show', [
                'id' => $assignment->getId(),
            ]);
        }

        return $this->render(
            'assignment/show.html.twig',
            [
                'assignment' => $assignment,
                'grade_form' => $gradeForm->createView(),
            ]
        );
    }
}
