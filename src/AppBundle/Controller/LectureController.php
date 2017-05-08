<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lecture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\Admin\CommentTrait;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

/**
 * Lecture controller.
 *
 * @Route("lecture")
 */
class LectureController extends Controller
{
    use CommentTrait;

    /**
     * Lists all lecture entities.
     *
     * @Route("/", name="lecture_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $lecture = new Lecture();
        $this->denyAccessUnlessGranted('list', $lecture);

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
     * @Method({"GET", "POST"})
     *
     * @param Lecture $lecture
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Lecture $lecture)
    {
        $this->denyAccessUnlessGranted('show', $lecture);

        $comments = $this->getEntityComments($lecture);
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->denyAccessUnlessGranted('new', $comment);

            $comment->setAuthor($this->getUser());
            $comment = $commentForm->getData();
            $thread = $this->getThreadPromise($lecture);
            $comment->setThread($thread);
            $thread->addComment($comment);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute(
                'lecture_show',
                [
                    'id' => $lecture->getId()
                ]
            );
        }

        return $this->render(
            'lecture/show.html.twig',
            [
                'lecture' => $lecture,
                'comments' => $comments,
                'comment_form' => $commentForm->createView()
            ]
        );
    }
}
