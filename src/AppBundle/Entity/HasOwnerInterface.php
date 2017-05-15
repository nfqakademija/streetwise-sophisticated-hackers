<?php

namespace AppBundle\Entity;

/**
 * Interface HasOwnerInterface
 * @package AppBundle\Entity
 */
interface HasOwnerInterface
{
    /**
     * Returns Entity owner (eg. User - itself, Lecture - lecturer, News - author
     * @return User
     */
    public function getOwner();
}
