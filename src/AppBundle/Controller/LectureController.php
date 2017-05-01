<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lecture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Lecture controller.
 *
 * @Route("lecture")
 */
class LectureController extends Controller
{
    /**
     * Lists all lecture entities.
     *
     * @Route("/", name="lecture_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

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
     * @Route("/{id}", name="lecture_show")
     * @Method("GET")
     */
    public function showAction(Lecture $lecture)
    {

        return $this->render(
            'lecture/show.html.twig',
            [
                'lecture' => $lecture,
            ]
        );
    }
}
