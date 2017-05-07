<?php

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\GradeType;

/**
 * Class AssignmentController
 * @package AppBundle\Controller\Admin
 */
class AssignmentController extends BaseAdminController
{
    /**
     * @return RedirectResponse|Response
     */
    protected function showAssignmentAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $this->denyAccessUnlessGranted('show', $entity);

        $gradeForm = $this->createForm(GradeType::class, $entity);
        $gradeForm->handleRequest($this->request);

        if ($gradeForm->isSubmitted() && $gradeForm->isValid()) {
            $this->denyAccessUnlessGranted('grade', $entity);
            $assignment = $gradeForm->getData();

            $this->em->persist($assignment);
            $this->em->flush();

            return $this->redirectToRoute(
                'easyadmin',
                [
                    'action' => 'show',
                    'entity' => 'Assignment',
                    'id' => $entity->getId(),
                ]
            );
        }

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
                'grade_form' => $gradeForm->createView(),
            ]
        );
    }

    /**
     * @Route("/download/homework/{file}", name="homework_download")
     *
     * @param string $file
     * @return Response
     */
    public function downloadAssignmentAction(string $file)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');

        $assignment = $em->getRepository('AppBundle:Assignment')
            ->findOneByWork($file);

        $this->denyAccessUnlessGranted('show', $assignment);

        $downloadHandler = $this->get('vich_uploader.download_handler');

        return $downloadHandler->downloadObject($assignment, $fileField = 'workFile');
    }
}
