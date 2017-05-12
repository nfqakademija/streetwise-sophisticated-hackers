<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * Assignment
 *
 * @ORM\Table(name="assignment")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Assignment implements HasOwnerInterface
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
     * @var User $student
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $student;

    /**
     * @var Homework $homework
     *
     * @ORM\ManyToOne(targetEntity="Homework", inversedBy="assignments")
     * @ORM\JoinColumn(name="homework_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $homework;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="grade", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 1,
     *      max = 10,
     *      minMessage = "Grade must be at least {{ limit }}",
     *      maxMessage = "Grade must be no more than {{ limit }}"
     * )
     */
    private $grade;

    /**
     * @var string $work
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $work;

    /**
     * @var File $workFile
     *
     * @Vich\UploadableField(mapping="work_file", fileNameProperty="work")
     */
    private $workFile;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CommentThread", cascade={"all"})
     */
    private $thread;

    /**
     * @return User
     */
    public function getStudent(): User
    {
        return $this->student;
    }

    /**
     * @param User $student
     */
    public function setStudent(User $student)
    {
        $this->student = $student;
    }

    /**
     * @return Homework
     */
    public function getHomework()
    {
        return $this->homework;
    }

    /**
     * @param Homework $homework
     */
    public function setHomework(Homework $homework)
    {
        $this->homework = $homework;
    }

    /**
     * @return string
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * @param string $work
     */
    public function setWork($work)
    {
        $this->work = $work;
    }

    /**
     * @return File
     */
    public function getWorkFile()
    {
        return $this->workFile;
    }

    /**
     * @param File $workFile
     */
    public function setWorkFile(File $workFile)
    {
        $this->workFile = $workFile;
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Assignment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set grade
     *
     * @param integer $grade
     *
     * @return Assignment
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return int
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @inheritDoc
     */
    public function getOwner(): User
    {
        return $this->student;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Assignment #' . $this->id;
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
     * @return Assignment
     */
    public function setThread($thread)
    {
        $this->thread = $thread;

        return $this;
    }
}
