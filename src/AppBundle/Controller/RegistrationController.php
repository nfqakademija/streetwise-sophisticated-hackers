<?php
/**
 * Created by PhpStorm.
 * User: eleggua
 * Date: 17.4.9
 * Time: 14.36
 */

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

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