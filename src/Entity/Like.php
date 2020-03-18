<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeRepository")
 * @ORM\Table(name="`like`")
 */
class Like
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Proposal", inversedBy="likes")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"user", "comment", "like"})
     */
    private $proposal_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"proposal", "review", "like"})
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UploadedDocument", inversedBy="likes")
     */
    private $uploaded_document_id;

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
