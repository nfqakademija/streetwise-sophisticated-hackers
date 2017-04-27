<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

/**
 * Class RegistrationController
 * @package AppBundle\Controller
 */
class RegistrationController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function registerAction(Request $request)
    {
        return $this->redirectToRoute('easyadmin', [
            'action' => 'new',
            'entity' => 'User',
        ]);
    }
}
