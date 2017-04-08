<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Controller\ProfileController as BaseController;

/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends BaseController
{
    /**
     * Show the user.
     */
    public function showAction()
    {
        return $this->redirect("/admin/?action=show&entity=User&id=".$this->getUser()->getId());
    }

    /**
     * Edit the user.
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
