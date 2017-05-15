<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="news")
 */
class News implements
    HasOwnerInterface,
    HasStudentGroupInterface
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Type("string")
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime
     * @Assert\NotBlank()
     */
    private $date;

    /**
     * @var User $author
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $author;

    /**
     * @ORM\OneToOne(
     *     targetEntity="AppBundle\Entity\CommentThread",
     *     cascade={"all"}
     *     )
     */
    private $thread;

    /**
     * @var
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\StudentGroup",
     *     inversedBy="news"
     * )
     */
    protected $studentgroup;

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
     * Set date
     *
     * @param \DateTime $date
     * @return $this
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
     * @param User $author
     * @return $this
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Set thread
     * @param \AppBundle\Entity\CommentThread $thread
     * @return News
     */
    public function setThread(CommentThread $thread = null)
    {
        $this->thread = $thread;

        return $this;
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
     * Set studentgroup
     *
     * @param \AppBundle\Entity\StudentGroup $studentgroup
     *
     * @return News
     */
    public function setStudentgroup(StudentGroup $studentgroup = null)
    {
        $this->studentgroup = $studentgroup;

        return $this;
    }

    /**
     * Get studentgroup
     *
     * @return \AppBundle\Entity\StudentGroup
     */
    public function getStudentgroup()
    {
        return $this->studentgroup;
    }
}
