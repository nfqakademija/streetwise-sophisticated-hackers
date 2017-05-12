<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = new User();
        $this->denyAccessUnlessGranted('list', $user);
        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->getRepository('AppBundle:User')
            ->findBy(
                [],
                ['name' => 'ASC']
            );

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render(
            'user/index.html.twig',
            [
                'users' => $pagination,
            ]
        );
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(User $user)
    {
        $this->denyAccessUnlessGranted('show', $user);

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
