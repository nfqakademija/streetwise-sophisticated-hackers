<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * Lecture
 *
 * @ORM\Table(name="lecture")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Lecture implements HasOwnerInterface
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
     * @ORM\Column(name="date", type="date")
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min = "now",
     *     max = "+2 years",
     *     groups="create"
     * )
     */
    private $date;

    /**
     * @var User $lecturer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="lecturer_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $lecturer;

    /**
     * @var string $slides
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slides;

    /**
     * @var File $slidesFile
     *
     * @Vich\UploadableField(mapping="lecture_slides", fileNameProperty="slides")
     */
    private $slidesFile;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CommentThread", cascade={"all"})
     */
    private $thread;

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
     * @return string
     */
    public function getSlides()
    {
        return $this->slides;
    }

    /**
     * @param string $slides
     */
    public function setSlides(string $slides = null)
    {
        $this->slides = $slides;
    }

    /**
     * @return File
     */
    public function getSlidesFile()
    {
        return $this->slidesFile;
    }

    /**
     * @param File $slidesFile
     */
    public function setSlidesFile(File $slidesFile = null)
    {
        $this->slidesFile = $slidesFile;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($slidesFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner(): User
    {
        return $this->lecturer;
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
     * @return Lecture
     */
    public function setThread($thread)
    {
        $this->thread = $thread;

        return $this;
    }
}
