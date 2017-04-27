<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\CommentThread;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use AppBundle\Entity\News;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\NewsType;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

/**
 * Class NewsController
 * @package AppBundle\Controller\Admin
 */
class NewsController extends BaseAdminController
{
    /**
     * {@inheritdoc}
     *
     * @return Response|RedirectResponse
     */
    public function editNewsAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('edit', $entity);

        return parent::editAction();
    }

    /**
     * {@inheritdoc}
     *
     * @return RedirectResponse
     */
    public function deleteNewsAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('delete', $entity);

        return parent::deleteAction();
    }

    /**
     * {@inheritdoc}
     *
     * @return Response|RedirectResponse
     */
    protected function newNewsAction()
    {
        $news = new News();
        $this->denyAccessUnlessGranted('new', $news);

        $news->setDate(new \DateTime());
        $news->setAuthor($this->getUser());

        $newsForm = $this->createForm(NewsType::class, $news);

        $newsForm->handleRequest($this->request);
        if ($newsForm->isSubmitted() && $newsForm->isValid()) {
            $this->denyAccessUnlessGranted('new', $news);

            $news = $newsForm->getData();
            $news->setDate(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            $queryParameters = [
                'action' => 'list',
                'entity' => 'News'
            ];

            return $this->redirect($this->get('router')->generate('easyadmin', $queryParameters));
        }

        return $this->render($this->entity['templates']['new'], array(
                'form' => $newsForm->createView()
            ));
    }

    /**
     * @param int $commentThreadId
     * @return CommentThread | null
     */
    private function getThreadById(int $commentThreadId)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        return $em->createQueryBuilder()
            ->select('e')
            ->from('AppBundle:CommentThread', 'e')
            ->where('e.id = ' . $commentThreadId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     *
     * @return Response
     */
    protected function showNewsAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $comments = null;
        if ($entity->getThreadId() != null) {
            $thread = $this->getThreadById($entity->getThreadId());
            if ($thread != null) {
                $comments = $thread->getComments();
            }
        }

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($this->request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->denyAccessUnlessGranted('new', $comment);

            $comment->setAuthor($this->getUser());
            $comment = $commentForm->getData();

            $em = $this->get('doctrine.orm.default_entity_manager');
            if (($entity->getThreadId() == null) or ($thread == null)) {
                $thread = new CommentThread();
                $thread->setOwnerName($entity->__toString());
                $em->persist($thread);
                $em->flush();
                $entity->setThreadId($thread->getId());
                $em->persist($entity);
            }

            $comment->setThread($thread);
            $thread->addComment($comment);
            $em->persist($comment);
            $em->flush();

            $queryParameters = [
                'action' => 'show',
                'entity' => $this->entity['name'],
                'id' => $id,
            ];
            return $this->redirect($this->get('router')->generate('easyadmin', $queryParameters));
        }

        $fields = $this->entity['show']['fields'];
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $this->dispatch(EasyAdminEvents::POST_SHOW, array(
            'deleteForm' => $deleteForm,
            'fields' => $fields,
            'entity' => $entity,
        ));

        return $this->render($this->entity['templates']['show'], array(
            'entity' => $entity,
            'fields' => $fields,
            'delete_form' => $deleteForm->createView(),
            'comment_form' => $commentForm->createView(),
            'comments' => $comments,
        ));
    }
}
