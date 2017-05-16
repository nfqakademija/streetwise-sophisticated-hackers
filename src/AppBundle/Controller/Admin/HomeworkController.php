<?php

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use AppBundle\Entity\Homework;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HomeworkController
 * @package AppBundle\Controller\Admin
 */
class HomeworkController extends BaseAdminController
{
    /**
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
     * @return Response|RedirectResponse
     */
    protected function newHomeworkAction()
    {
        $homework = new Homework();
        $this->denyAccessUnlessGranted('new', $homework);

        return parent::newAction();
    }

    /**
     * @return RedirectResponse|Response
     */
    protected function showHomeworkAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $this->denyAccessUnlessGranted('show', $entity);

        $assignments = $entity->getAssignments();

        $fields = $this->entity['show']['fields'];
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $this->dispatch(
            EasyAdminEvents::POST_SHOW,
            [
                'deleteForm' => $deleteForm,
                'fields' => $fields,
                'entity' => $entity,
            ]
        );

        return $this->render(
            $this->entity['templates']['show'],
            [
                'entity' => $entity,
                'fields' => $fields,
                'delete_form' => $deleteForm->createView(),
                'assignments' => $assignments,
            ]
        );
    }
}
