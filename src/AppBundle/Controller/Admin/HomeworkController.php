<?php
/**
 * Created by PhpStorm.
 * User: eleggua
 * Date: 17.4.11
 * Time: 21.50
 */

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

        $assignment = new Assignment();
        $assignment->setDate(new \DateTime());
        $assignment->setHomework($entity);
        $assignment->setStudent($this->getUser());

        $assignmentForm = $this->createForm(AssignmentType::class, $assignment);

        $fields = $this->entity['show']['fields'];
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $this->dispatch(EasyAdminEvents::POST_SHOW, array(
            'deleteForm' => $deleteForm,
            'fields' => $fields,
            'entity' => $entity,
        ));

        $assignmentForm->handleRequest($this->request);

        if ($assignmentForm->isSubmitted() && $assignmentForm->isValid()) {
            $assignment = $assignmentForm->getData();
            $assignment->setDate(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($assignment);
            $em->flush();

            return $this->redirect("/admin/?action=show&entity=Homework&id=".$entity->getId());
        }

        return $this->render($this->entity['templates']['show'], array(
            'entity' => $entity,
            'fields' => $fields,
            'delete_form' => $deleteForm->createView(),
            'assignment_form' => $assignmentForm->createView(),
            'assignments' => $assignments,
        ));
    }
}
