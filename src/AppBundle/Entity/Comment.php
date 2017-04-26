<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="comments")
 */
class Comment implements HasOwnerInterface
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    // TODO: remove this
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\News", inversedBy="comments")
     * @ORM\JoinColumn(fieldName="news id", name="news_id", referencedColumnName="id")
     */
    private $newsId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CommentThread", inversedBy="comments")
     * @ORM\JoinColumn(fieldName="thread id", name="thread_id", referencedColumnName="id")
     */
    private $thread;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank(message="comment.blank")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $date;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function __construct()
    {
        $this->date = new \DateTime();
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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Comment
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
     * Set newsId
     *
     * @param \AppBundle\Entity\News $newsId
     */
    public function setNewsId(News $newsId = null)
    {
        $this->newsId = $newsId;
    }

    /**
     * Get newsId
     *
     * @return \AppBundle\Entity\News
     */
    public function getNewsId()
    {
        return $this->newsId;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $author
     *
     * @return Comment
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @inheritDoc
     */
    public function getOwner(): User
    {
        return $this->author;
    }

    /**
     * Set thread
     *
     * @param \AppBundle\Entity\CommentThread $thread
     *
     * @return Comment
     */
    public function setThread(CommentThread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \AppBundle\Entity\CommentThread
     */
    public function getThread()
    {
        return $this->thread;
    }
}
