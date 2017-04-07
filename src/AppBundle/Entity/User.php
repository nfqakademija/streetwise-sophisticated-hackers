<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @Assert\Email()
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @Assert\Length(min=6,max=255)
     * @Assert\NotBlank(groups="registration")
     */
    protected $plainPassword;

    protected $confirmPassword;

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param mixed $confirmPassword
     */
    public function setConfirmPassword($confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @Assert\IsTrue(message = "Passwords are not the same")
     */
    public function isPasswordLegal()
    {
        return ($this->plainPassword == $this->confirmPassword);
    }
}