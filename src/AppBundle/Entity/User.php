<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements ParticipantInterface, HasOwnerInterface
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
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string $email
     *
     * @Assert\Email()
     * @Assert\Length(max=180)
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @var string $plainPassword
     * @Assert\Type("string")
     * @Assert\Length(min=6,max=72)
     * @Assert\NotBlank(groups="registration")
     */
    protected $plainPassword;

    /**
     * @var string $occupation
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type("string")
     * @Assert\Length(max=255)
     */
    protected $occupation;

    /**
     * @var string $interests
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type("string")
     * @Assert\Length(max=255)
     */
    protected $interests;

    /**
     * @var StudentGroup $studentGroup
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\StudentGroup",
     *     inversedBy="students"
     * )
     */
    protected $studentGroup;

    public function __construct()
    {
        parent::__construct();
        $this->name = '';
    }

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
     * @return bool
     */
    public function isStudent():bool
    {
        return (!in_array('ROLE_SUPER_ADMIN', $this->roles)
            && !in_array('ROLE_ADMIN', $this->roles)
            && !in_array('ROLE_LECTOR', $this->roles));
    }

    /**
     * @return bool
     */
    public function isLector():bool
    {
        return (in_array('ROLE_LECTOR', $this->roles));
    }

    /**
     * @return string
     */
    public function getRole()
    {
        if (in_array('ROLE_SUPER_ADMIN', $this->roles)) {
            return "Administrator";
        }

        if (in_array('ROLE_ADMIN', $this->roles)) {
            return "Administrator";
        }

        if (in_array('ROLE_LECTOR', $this->roles)) {
            return "Lecturer";
        }

        return "Student";
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner(): User
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGravatar()
    {
        return md5($this->getEmail());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set studentGroup
     *
     * @param \AppBundle\Entity\StudentGroup $studentGroup
     *
     * @return User
     */
    public function setStudentGroup(StudentGroup $studentGroup = null)
    {
        $this->studentGroup = $studentGroup;

        return $this;
    }

    /**
     * Get studentGroup
     *
     * @return \AppBundle\Entity\StudentGroup
     */
    public function getStudentGroup()
    {
        return $this->studentGroup;
    }
}
