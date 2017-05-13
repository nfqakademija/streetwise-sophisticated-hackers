<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="student_group")
 */
class StudentGroup
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
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var User[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="User",
     *     mappedBy="studentGroup",
     *     cascade={"all"}
     * )
     */
    protected $students;

    /**
     * @var News[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\News",
     *     mappedBy="studentGroup",
     *     cascade={"all"}
     * )
     */
    protected $news;

    /**
     * @var Lecture[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Lecture",
     *     mappedBy="studentGroup",
     *     cascade={"all"}
     * )
     */
    protected $lectures;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return StudentGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add student
     *
     * @param \AppBundle\Entity\User $student
     *
     * @return StudentGroup
     */
    public function addStudent(User $student)
    {
        $this->students[] = $student;

        return $this;
    }

    /**
     * Remove student
     *
     * @param \AppBundle\Entity\User $student
     */
    public function removeStudent(User $student)
    {
        $this->students->removeElement($student);
    }

    public function __toString()
    {
        return "StudentGroup #" . $this->id;
    }

    /**
     * Add news
     *
     * @param \AppBundle\Entity\News $news
     *
     * @return StudentGroup
     */
    public function addNews(News $news)
    {
        $this->news[] = $news;

        return $this;
    }

    /**
     * Remove news
     *
     * @param \AppBundle\Entity\News $news
     */
    public function removeNews(News $news)
    {
        $this->news->removeElement($news);
    }

    /**
     * Get news
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Add lecture
     *
     * @param \AppBundle\Entity\Lecture $lecture
     *
     * @return StudentGroup
     */
    public function addLecture(Lecture $lecture)
    {
        $this->lectures[] = $lecture;

        return $this;
    }

    /**
     * Remove lecture
     *
     * @param \AppBundle\Entity\Lecture $lecture
     */
    public function removeLecture(Lecture $lecture)
    {
        $this->lectures->removeElement($lecture);
    }

    /**
     * Get lectures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLectures()
    {
        return $this->lectures;
    }
}
