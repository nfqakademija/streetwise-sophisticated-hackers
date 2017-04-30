<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\CommentThread;

trait CommentTrait
{
    /**
     * @param $entity
     * @return null
     */
    private function getEntityComments($entity)
    {
        if ($entity->getThread() == null) {
            return null;
        }
        return $entity->getThread()->getComments();
    }

    /**
     * Return Entity's CommentThread
     * If null, create, set and return empty CommentThread
     * @param $entity
     * @return CommentThread
     */
    private function getThreadPromise($entity)
    {
        $thread = $entity->getThread();
        if ($thread != null) {
            return $thread;
        }
        $thread = new CommentThread();
        $thread->setOwnerName($entity->__toString());
        $entity->setThread($thread);
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($entity);
        $em->flush();
        return $thread;
    }
}
