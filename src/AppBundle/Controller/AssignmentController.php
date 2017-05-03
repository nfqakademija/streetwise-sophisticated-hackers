<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Assignment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
     * @Method("GET")
     */
    public function showAction(Assignment $assignment)
    {

        $this->denyAccessUnlessGranted('show', $assignment);

        return $this->render(
            'assignment/show.html.twig',
            [
                'assignment' => $assignment,
            ]
        );
    }
}
