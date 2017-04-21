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
class User extends BaseUser implements HasOwnerInterface
{
    /**
     * @var string $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string $email
     *
     * @Assert\Email()
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @var string $plainPassword
     *
     * @Assert\Length(min=6,max=255)
     * @Assert\NotBlank(groups="registration")
     */
    protected $plainPassword;

    /**
     * @var string $confirmPassword
     */
    protected $confirmPassword;

    /**
     * @return string
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param string $confirmPassword
     */
    public function setConfirmPassword(string $confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @Assert\IsTrue(message = "Passwords are not the same")
     *
     * @return boolean
     */
    public function isPasswordLegal()
    {
        return ($this->plainPassword == $this->confirmPassword);
    }

    /**
     * @return bool
     */
    public function isStudent()
    {
        return (!in_array('ROLE_ADMIN', $this->roles) && !in_array('ROLE_LECTOR', $this->roles));
    }

    public function getOwner(): User
    {
        return $this;
    }
}
