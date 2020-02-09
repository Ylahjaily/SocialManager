<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UploadedDocumentRepository")
 */
class UploadedDocument
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="blob")
     */
    private $data;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Proposal", inversedBy="uploadedDocument", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $proposal_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getProposalId(): ?Proposal
    {
        return $this->proposal_id;
    }

    public function setProposalId(Proposal $proposal_id): self
    {
        $this->proposal_id = $proposal_id;

        return $this;
    }
}
