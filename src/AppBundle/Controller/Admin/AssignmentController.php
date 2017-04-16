<?php

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AssignmentController
 * @package AppBundle\Controller\Admin
 */
class AssignmentController extends BaseAdminController
{
    /**
     * @return RedirectResponse|Response
     */
    protected function showAssignmentAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('show', $entity);

        return parent::showAction();
    }
}
