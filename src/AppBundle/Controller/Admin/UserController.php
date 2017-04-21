<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\UserType;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package AppBundle\Controller\Admin
 */
class UserController extends BaseAdminController
{
    /**
     * {@inheritdoc}
     *
     * @return User
     */
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    /**
     * {@inheritdoc}
     *
     * @param User $user
     */
    public function prePersistUserEntity(User $user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    /**
     * {@inheritdoc}
     *
     * @param User $user
     */
    public function preUpdateUserEntity(User $user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    /**
     * {@inheritdoc}
     *
     * @return Response|RedirectResponse
     */
    public function editUserAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('edit', $entity);

        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $this->denyAccessUnlessGranted('edit', $entity);

        //TODO: updateEntityProperty is private
        /*if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            return new Response((string) $newValue);
        }*/

        $fields = $this->entity['edit']['fields'];

        if (!$this->getUser()->hasRole('ROLE_ADMIN') && $this->getUser()->getId() == $this->request->query->get('id')) {
            $editForm = $this->createForm(UserType::class, $entity);
        } else {
            $editForm = $this->createEditForm($entity, $fields);
        }

        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, array('entity' => $entity));

            $this->preUpdateEntity($entity);
            $this->em->flush();

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
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

    /**
     * @return RedirectResponse
     */
    public function deleteUserAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('delete', $entity);

        return parent::deleteAction();
    }

    /**
     * @return Response|RedirectResponse
     */
    protected function newUserAction()
    {
        $user = new User();
        $this->denyAccessUnlessGranted('new', $user);

        return parent::newAction();
    }

    protected function showUserAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $fields = $this->entity['show']['fields'];
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $lectures = null;

        if($entity->hasRole('ROLE_LECTOR')) {
            $em = $this->getDoctrine()->getManager();

            $lectures = $em->getRepository('AppBundle:Lecture')
                ->findBy(
                    [
                        'lecturer' => $entity->getId()
                    ]
                );
        }

        $this->dispatch(EasyAdminEvents::POST_SHOW, array(
            'deleteForm' => $deleteForm,
            'fields' => $fields,
            'entity' => $entity,
        ));

        return $this->render($this->entity['templates']['show'], array(
            'entity' => $entity,
            'fields' => $fields,
            'delete_form' => $deleteForm->createView(),
            'lectures' => $lectures
        ));
    }
}
