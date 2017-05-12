<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\MessageType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * Send a message to particular user
     *
     * @Route("/message/{id}", name="user_message")
     * @Method({"GET", "POST"})
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function messageAction(User $user, Request $request)
    {
        if ($user == $this->getUser()) {
            return $this->redirectToRoute('fos_message_thread_new');
        }

        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $threadBuilder = $this->get('fos_message.composer')->newThread();
            $threadBuilder
                ->addRecipient($user)
                ->setSender($this->getUser())
                ->setSubject($data['subject'])
                ->setBody($data['body']);

            $sender = $this->get('fos_message.sender');
            $message = $threadBuilder->getMessage();
            $sender->send($message);

            return $this->redirectToRoute(
                'fos_message_thread_view',
                [
                    'threadId' => $message->getThread()->getId(),
                ]
            );
        }

        return $this->render(
            'FOSMessageBundle:Message:newThread.html.twig',
            [
                'form' => $form->createView(),
                'data' => $form->getData(),
                'recipient' => $user,
            ]
        );
    }
}
