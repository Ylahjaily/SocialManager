<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 */
class Review
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment", "uploadedDoc"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Proposal", inversedBy="reviews")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"user", "review", "reviewComment"})
     */
    private $proposal_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"review", "comment", "reviewComment", "proposal"})
     */
    private $user_id;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment", "uploadedDoc"})
     */
    private $is_approved;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment", "uploadedDoc"})
     */
    private $decision_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReviewComment", mappedBy="review_id", orphanRemoval=true)
     * @Groups({"user", "review", "comment", "like", "uploadedDoc"})
     */
    private $reviewComments;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment", "uploadedDoc"})
     * @Assert\NotBlank()
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UploadedDocument", inversedBy="reviews")
     * @Groups({"review"})
     */
    private $uploaded_document_id;

    public function __construct()
    {
        $this->created_at = new \DateTime('now');
        $this->reviewComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProposalId(): ?Proposal
    {
        return $this->proposal_id;
    }

    public function setProposalId(?Proposal $proposal_id): self
    {
        $this->proposal_id = $proposal_id;

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

    public function getIsApproved(): ?bool
    {
        return $this->is_approved;
    }

    public function setIsApproved(bool $is_approved): self
    {
        $this->is_approved = $is_approved;

        return $this;
    }

    public function getDecisionAt(): ?\DateTimeInterface
    {
        return $this->decision_at;
    }

    public function setDecisionAt(\DateTimeInterface $decision_at): self
    {
        $this->decision_at = $decision_at;

        return $this;
    }

    /**
     * @return Collection|ReviewComment[]
     */
    public function getReviewComments(): Collection
    {
        return $this->reviewComments;
    }

    public function addReviewComment(ReviewComment $reviewComment): self
    {
        if (!$this->reviewComments->contains($reviewComment)) {
            $this->reviewComments[] = $reviewComment;
            $reviewComment->setReviewId($this);
        }

        return $this;
    }

    public function removeReviewComment(ReviewComment $reviewComment): self
    {
        if ($this->reviewComments->contains($reviewComment)) {
            $this->reviewComments->removeElement($reviewComment);
            // set the owning side to null (unless already changed)
            if ($reviewComment->getReviewId() === $this) {
                $reviewComment->setReviewId(null);
            }
        }

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

    public function getUploadedDocumentId(): ?UploadedDocument
    {
        return $this->uploaded_document_id;
    }

    public function setUploadedDocumentId(?UploadedDocument $uploaded_document_id): self
    {
        $this->uploaded_document_id = $uploaded_document_id;

        return $this;
    }
}
