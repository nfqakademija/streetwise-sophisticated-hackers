<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Lecture
 *
 * @ORM\Table(name="lecture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LectureRepository")
 * @Vich\Uploadable
 */
class Lecture
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
     */
    private $date;

    /**
     * @var User $lecturer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="lecturer_id", referencedColumnName="id")
     */
    private $lecturer;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slides;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="lecture_slides", fileNameProperty="slides")
     */
    private $slidesFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     */
    private $updatedAt;

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
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
     *
     * @return Lecture
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
     *
     * @return Lecture
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
     *
     * @return Lecture
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
     *
     * @return Lecture
     */
    public function setLecturer(User $lecturer)
    {
        $this->lecturer = $lecturer;

        return $this;
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
    public function getSlides(): string
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
}
