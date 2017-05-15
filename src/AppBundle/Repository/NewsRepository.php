<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class NewsRepository
 * @package AppBundle\Repository
 */
class NewsRepository extends EntityRepository
{
    /**
     * @param int $group
     * @return array
     */
    public function findGroupAndPublic($group)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n 
                FROM AppBundle:News n 
                WHERE n.studentgroup = :group 
                OR n.studentgroup IS NULL 
                ORDER BY n.date DESC'
            )
            ->setParameter('group', $group)
            ->getResult();
    }

    /**
     * @return array
     */
    public function findPublic()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n 
                FROM AppBundle:News n 
                WHERE n.studentgroup IS NULL 
                ORDER BY n.date DESC'
            )
            ->getResult();
    }
}
