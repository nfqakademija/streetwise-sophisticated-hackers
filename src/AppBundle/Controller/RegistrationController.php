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
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        return $this->redirect("/admin/?action=new&entity=User");
    }
}
