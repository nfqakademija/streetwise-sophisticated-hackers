<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * User controller.
 *
 * @Route("user")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render(
            'user/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $lectures = [];
        $assignments = [];

        if ($user->isLector()) {
            $lectures = $em->getRepository('AppBundle:Lecture')
                ->findBy(
                    [
                        'lecturer' => $user->getId()
                    ]
                );
        } elseif ($user->isStudent()) {
            $assignments = $em->getRepository('AppBundle:Assignment')
                ->findBy(
                    [
                        'student' => $user->getId()
                    ]
                );
        }

        return $this->render(
            'user/show.html.twig',
            [
                'user' => $user,
                'lectures' => $lectures,
                'assignments' => $assignments,
            ]
        );
    }
}
