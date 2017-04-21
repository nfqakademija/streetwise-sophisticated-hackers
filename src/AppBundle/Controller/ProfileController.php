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
     *
     * @return RedirectResponse
     */
    public function showAction()
    {
        $queryParameters = [
            'action' => 'show',
            'entity' => 'User',
            'id' => $this->getUser()->getId()
        ];

        return $this->redirect($this->get('router')->generate('easyadmin', $queryParameters));
    }

    /**
     * {@inheritdoc}
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $queryParameters = [
            'action' => 'edit',
            'entity' => 'User',
            'id' => $this->getUser()->getId()
        ];

        return $this->redirect($this->get('router')->generate('easyadmin', $queryParameters));
    }
}
