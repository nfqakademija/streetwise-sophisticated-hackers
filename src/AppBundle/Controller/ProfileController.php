<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Controller\ProfileController as BaseController;

/**
 * Class ProfileController
 * @package AppBundle\Controller
 */
class ProfileController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function showAction()
    {
        return $this->redirectToRoute('easyadmin', [
            'action' => 'show',
            'entity' => 'User',
            'id' => $this->getUser()->getId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function editAction(Request $request)
    {
        return $this->redirectToRoute('easyadmin', [
            'action' => 'edit',
            'entity' => 'User',
            'id' => $this->getUser()->getId(),
        ]);
    }
}
