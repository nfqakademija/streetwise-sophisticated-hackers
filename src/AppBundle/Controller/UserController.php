<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Form\UserBigType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/{id}", name="user_show", requirements={"id": "\d+"})
     * @ParamConverter("user", class="AppBundle:User", options={"id" = "id"})
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

    /**
     * @Route("/edit/", name="user_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function editUserAction(Request $request)
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('edit', $user);
        $editForm = $this->createForm(UserBigType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $user = $editForm->getData();

            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($user);
            $em->flush();

            $refererUrl = $request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirectToRoute(
                    'user_show',
                    [
                        'id' => $user->getId()
                    ]
                );
        }

        return $this->render(
            '@AppBundle/views/user/edit.html.twig',
            [
                'editForm' => $editForm->createView(),
                'entity' => $user,
            ]
        );
    }

    /**
     * @Route("/change/", name="user_change")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('edit', $user);

        $editForm = $this->createForm(ChangePasswordFormType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $user = $editForm->getData();

            if ($user->getPlainPassword() != null) {
                $encoder = $this->container->get('security.password_encoder');
                $password = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->eraseCredentials();
            }

            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($user);
            $em->flush();

            $refererUrl = $request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirectToRoute(
                    'user_show',
                    [
                        'id' => $user->getId()
                    ]
                );
        }

        return $this->render(
            '@AppBundle/views/user/changePassword.html.twig',
            [
                'editForm' => $editForm->createView(),
                'entity' => $user,
            ]
        );
    }
}
