<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Homework
 *
 * @ORM\Table(name="homework")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HomeworkRepository")
 */
class Homework implements HasOwnerInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetime")
     */
    private $dueDate;

    /**
     * @var User $lecturer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="lecturer_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $lecturer;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Assignment", mappedBy="homework", cascade={"remove"})
     */
    private $assignments;

    /**
     * @return mixed
     */
    public function getAssignments()
    {
        return $this->assignments;
    }

    /**
     * @param mixed $assignments
     */
    public function setAssignments($assignments)
    {
        $this->assignments = $assignments;
    }

    /**
     * Homework constructor.
     */
    public function __construct()
    {
        $this->assignments = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set lecturer
     *
     * @param User $lecturer
     */
    public function setLecturer(User $lecturer)
    {
        $this->lecturer = $lecturer;
    }

    /**
     * Get lecturer
     *
     * @return User
     */
    public function getLecturer()
    {
        return $this->lecturer;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner(): User
    {
        return $this->lecturer;
    }
}

