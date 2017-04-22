<?php

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use AppBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommentController
 * @package AppBundle\Controller\Admin
 */
class CommentController extends BaseAdminController
{
    /**
     * {@inheritdoc}
     *
     * @return Response|RedirectResponse
     */
    public function editCommentAction()
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
    public function deleteCommentAction()
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
    protected function newCommentAction()
    {
        $comment = new Comment();
        $this->denyAccessUnlessGranted('new', $comment);

        return parent::newAction();
    }
}
