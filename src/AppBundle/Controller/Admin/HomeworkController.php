<?php
/**
 * Created by PhpStorm.
 * User: eleggua
 * Date: 17.4.11
 * Time: 21.50
 */

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use AppBundle\Entity\Homework;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeworkController
 * @package AppBundle\Controller\Admin
 */
class HomeworkController extends BaseAdminController
{
    /**
     * {@inheritdoc}
     *
     * @return Response|RedirectResponse
     */
    public function editHomeworkAction()
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
    public function deleteHomeworkAction()
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
    protected function newHomeworkAction()
    {
        $homework = new Homework();
        $this->denyAccessUnlessGranted('new', $homework);

        return parent::newAction();
    }
}
