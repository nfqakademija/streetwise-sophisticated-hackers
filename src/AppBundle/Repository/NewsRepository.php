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
        return $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('n')
            ->from('AppBundle:News', 'n')
            ->where('n.studentgroup = :group OR n.studentgroup IS NULL')
            ->orderBy('n.date', 'DESC')
            ->setParameter('group', $group)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findPublic()
    {
        return $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('n')
            ->from('AppBundle:News', 'n')
            ->where('n.studentgroup IS NULL')
            ->orderBy('n.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
