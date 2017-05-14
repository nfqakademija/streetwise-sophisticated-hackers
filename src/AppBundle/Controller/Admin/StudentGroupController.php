<?php

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class StudentGroupController extends BaseAdminController
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editStudentGroupAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('edit', $entity);

        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $entity->getId();

        $fields = $this->entity['edit']['fields'];
        $editForm = $this->createEditForm($entity, $fields);
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, ['entity' => $entity]);

            $this->preUpdateEntity($entity);
            $this->em->flush();

            $this->dispatch(EasyAdminEvents::POST_UPDATE, ['entity' => $entity]);

            return $this->redirectToRoute(
                'easyadmin',
                [
                    'action' => 'show',
                    'entity' => 'StudentGroup',
                    'id' => $id
                ]
            );
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        return $this->render(
            $this->entity['templates']['edit'],
            [
                'form' => $editForm->createView(),
                'entity_fields' => $fields,
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }
}
