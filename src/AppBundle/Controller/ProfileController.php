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
        return $this->redirect("/admin/?action=show&entity=User&id=".$this->getUser()->getId());
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
        return $this->redirect("/admin/?action=edit&entity=User&id=".$this->getUser()->getId());
    }
}
