<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewCommentRepository")
 */
class ReviewComment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"user", "review", "comment"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Review", inversedBy="reviewComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $review_id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user", "review", "comment"})
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reviewComments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"review"})
     */
    private $user_id;

    public function __construct()
    {
        $this->created_at = new \DateTime('now');
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getReviewId(): ?Review
    {
        return $this->review_id;
    }

    public function setReviewId(?Review $review_id): self
    {
        $this->review_id = $review_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
