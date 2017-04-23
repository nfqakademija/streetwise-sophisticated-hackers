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
    private $name;

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
    private $confirmPassword;

    /**
     * @var string $occupation
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $occupation;

    /**
     * @var string $interests
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $interests;

    /**
     * @return string
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * @param string $interests
     */
    public function setInterests(string $interests)
    {
        $this->interests = $interests;
    }

    /**
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param string $occupation
     */
    public function setOccupation(string $occupation)
    {
        $this->occupation = $occupation;
    }

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
    public function isStudent():bool
    {
        return (!in_array('ROLE_ADMIN', $this->roles) && !in_array('ROLE_LECTOR', $this->roles));
    }

    /**
     * @return bool
     */
    public function isLector():bool
    {
        return (in_array('ROLE_LECTOR', $this->roles));
    }

    public function getRole()
    {
        if(in_array('ROLE_ADMIN', $this->roles))
            return "Administrator";

        if(in_array('ROLE_LECTOR', $this->roles))
            return "Lecturer";

        return "Student";
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this;
    }

    public function getGravatar()
    {
        return md5($this->getEmail());
    }

    public function __toString()
    {
        return $this->name;
    }
}
