<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\UserType;
use AppBundle\Form\UserBigType;
use AppBundle\Form\UserFullType;
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
     * @return User
     */
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    /**
     * @param User $user
     */
    public function prePersistUserEntity(User $user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    /**
     * @param User $user
     */
    public function preUpdateUserEntity(User $user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    /**
     * @return Response|RedirectResponse
     */
    public function editUserAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('edit', $entity);

        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $entity->getId();

        $fields = $this->entity['edit']['fields'];

        if ($this->getUser()->getId() == $id) {
            $editForm = $this->createForm(UserBigType::class, $entity);
        } elseif ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $editForm = $this->createEditForm($entity, $fields);
        } elseif ($this->getUser()->hasRole('ROLE_ADMIN')) {
            $editForm = $this->createForm(UserFullType::class, $entity);
        } else {
            $editForm = $this->createForm(UserType::class, $entity);
        }

        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(
                EasyAdminEvents::PRE_UPDATE,
                [
                    'entity' => $entity
                ]
            );
            if ($entity->getPlainPassword() != null) {
                $encoder = $this->container->get('security.password_encoder');
                $password = $encoder->encodePassword($entity, $entity->getPlainPassword());
                $entity->setPassword($password);
            }

            $this->preUpdateEntity($entity);
            $this->em->flush();

            $this->dispatch(
                EasyAdminEvents::POST_UPDATE,
                [
                    'entity' => $entity
                ]
            );

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirectToRoute(
                    'easyadmin',
                    [
                        'action' => 'list',
                        'entity' => $this->entity['name'],
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

    /**
     * @return Response
     */
    protected function showUserAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);

        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $id = $entity->getId();

        $fields = $this->entity['show']['fields'];
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $lectures = [];
        $assignments = [];

        if ($entity->isLector()) {
            $lectures = $this->em->getRepository('AppBundle:Lecture')
                ->findBy(
                    [
                        'lecturer' => $id
                    ]
                );
        } elseif ($entity->isStudent()) {
            $assignments = $this->em->getRepository('AppBundle:Assignment')
                ->findBy(
                    [
                        'student' => $id
                    ]
                );
        }

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
                'lectures' => $lectures,
                'assignments' => $assignments,
            ]
        );
    }
}
