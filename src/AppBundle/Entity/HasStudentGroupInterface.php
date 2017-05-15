<?php

namespace AppBundle\Entity;

/**
 * Interface HasStudentGroupInterface
 * @package AppBundle\Entity
 */
interface HasStudentGroupInterface
{
    /**
     * Returns Entity owner (eg. User - itself, Lecture - lecturer, News - author
     * @return User
     */
    public function getOwner();

    /**
     * Returns Entity studentgroup|null
     * @return mixed
     */
    public function getStudentGroup();
}
