<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\CommentThread;
use AppBundle\Entity\Comment;

trait CommentTrait
{
    /**
     * @param $commentThreadId
     * @return CommentThread|null
     */
    private function getThreadById($commentThreadId)
    {
        if ($commentThreadId == null) {
            return null;
        }
        $em = $this->get('doctrine.orm.default_entity_manager');
        return $em->getRepository('AppBundle:CommentThread')->find($commentThreadId);
    }

    /**
     * @param $threadId
     * @return Comment[]|null
     */
    private function getCommentsFromThread($threadId)
    {
        $thread = $this->getThreadById($threadId);
        if ($thread == null) {
            return null;
        }
        return $thread->getComments();
    }

    /**
     * Return Entity's CommentThread
     * If null, create, set and return empty CommentThread
     * @param $entity
     * @return CommentThread|null
     */
    private function getThreadPromise($entity)
    {
        $thread = $this->getThreadById($entity->getThreadId());
        if ($thread == null) {
            $thread = new CommentThread();
            $thread->setOwnerName($entity->__toString());
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($thread);
            $em->flush();
            $entity->setThreadId($thread->getId());
            $em->persist($entity);
            $em->flush();
        }
        return $thread;
    }
}
