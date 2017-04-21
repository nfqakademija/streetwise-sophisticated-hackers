<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Assignment;
use AppBundle\Form\AssignmentType;
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

    protected function showHomeworkAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $assignments = $entity->getAssignments();

        $em = $this->getDoctrine()->getManager();

        $myAssignment = $em->getRepository('AppBundle:Assignment')
            ->findOneBy(
                [
                    'student' => $this->getUser()->getId(),
                    'homework' => $entity->getId()
                ]
            );

        $assignment = new Assignment();
        $assignment->setDate(new \DateTime());
        $assignment->setHomework($entity);
        $assignment->setStudent($this->getUser());

        $assignmentForm = $this->createForm(AssignmentType::class, $assignment);

        $assignmentForm->handleRequest($this->request);

        if ($assignmentForm->isSubmitted() && $assignmentForm->isValid() && $myAssignment == null) {
            $this->denyAccessUnlessGranted('new', $assignment);
            $assignment = $assignmentForm->getData();
            $assignment->setDate(new \DateTime());

            $em->persist($assignment);
            $em->flush();

            return $this->redirect("/admin/?action=show&entity=Homework&id=".$entity->getId());
        }

        $fields = $this->entity['show']['fields'];
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $this->dispatch(EasyAdminEvents::POST_SHOW, array(
            'deleteForm' => $deleteForm,
            'fields' => $fields,
            'entity' => $entity,
        ));


        return $this->render($this->entity['templates']['show'], array(
            'entity' => $entity,
            'fields' => $fields,
            'delete_form' => $deleteForm->createView(),
            'assignment_form' => $assignmentForm->createView(),
            'assignments' => $assignments,
            'my_assignment' => $myAssignment,
        ));
    }
}
