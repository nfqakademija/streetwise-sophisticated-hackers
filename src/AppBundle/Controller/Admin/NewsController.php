<?php

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use AppBundle\Entity\News;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

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

        return parent::newAction();
    }
}
