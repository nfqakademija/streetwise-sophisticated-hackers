<?php

namespace AppBundle\Controller\Admin;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use AppBundle\Entity\Lecture;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LectureController
 * @package AppBundle\Controller\Admin
 */
class LectureController extends BaseAdminController
{
    /**
     * @return Response|RedirectResponse
     */
    public function editLectureAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('edit', $entity);

        return parent::editAction();
    }

    /**
     * @return RedirectResponse
     */
    public function deleteLectureAction()
    {
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];
        $this->denyAccessUnlessGranted('delete', $entity);

        return parent::deleteAction();
    }

    /**
     * @return Response|RedirectResponse
     */
    protected function newLectureAction()
    {
        $lecture = new Lecture();
        $this->denyAccessUnlessGranted('new', $lecture);

        return parent::newAction();
    }

    /**
     * @Route("/admin/download/lecture/{file}", name="lecture_download")
     *
     * @param string $file
     * @return Response
     */
    public function downloadSlidesAction(string $file)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');

        $slides = $em->getRepository('AppBundle:Lecture')
            ->findOneBySlides($file);

        $this->denyAccessUnlessGranted('show', $slides);

        $downloadHandler = $this->get('vich_uploader.download_handler');

        return $downloadHandler->downloadObject($slides, $fileField = 'slidesFile');
    }
}
