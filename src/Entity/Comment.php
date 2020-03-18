<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Proposal", inversedBy="comments")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"user", "comment"})
     */
    private $proposal_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"proposal", "review", "comment"})
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user"})
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     * @Assert\NotBlank()
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UploadedDocument", inversedBy="comments")
     */
    private $uploaded_document_id;

    public function __construct()
    {
        $this->created_at = new \DateTime('now');
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
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
