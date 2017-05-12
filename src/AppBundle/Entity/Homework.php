<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Homework
 *
 * @ORM\Table(name="homework")
 * @ORM\Entity
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
     * @Assert\Type("string")
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
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
     * @Assert\DateTime
     * @Assert\Range(
     *     min = "now",
     *     max = "+2 years",
     *     groups="create"
     * )
     * @Assert\NotBlank()
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CommentThread", cascade={"all"})
     */
    private $thread;

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
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
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
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
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
     * @return $this
     */
    public function setDueDate(\DateTime $dueDate)
    {
        $this->dueDate = $dueDate;
        return $this;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Add assignment
     *
     * @param \AppBundle\Entity\Assignment $assignment
     *
     * @return Homework
     */
    public function addAssignment(\AppBundle\Entity\Assignment $assignment)
    {
        $this->assignments[] = $assignment;

        return $this;
    }

    /**
     * Remove assignment
     *
     * @param \AppBundle\Entity\Assignment $assignment
     */
    public function removeAssignment(\AppBundle\Entity\Assignment $assignment)
    {
        $this->assignments->removeElement($assignment);
    }

    /**
     * Get thread
     * @return \AppBundle\Entity\CommentThread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set thread
     * @param \AppBundle\Entity\CommentThread $thread
     * @return Homework
     */
    public function setThread($thread)
    {
        $this->thread = $thread;

        return $this;
    }
}
