<?php

namespace AppBundle\Entity;


interface HasOwnerInterface
{
    public function getOwner(): User;
}
