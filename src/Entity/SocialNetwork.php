<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocialNetworkRepository")
 */
class SocialNetwork
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user", "proposal", "review", "comment", "like", "social"})
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="socialNetworks")
     * @Groups({"proposal", "review", "comment", "social"})
     */
    private $user_id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Proposal", inversedBy="socialNetworks")
     * @Groups({"user", "review", "comment", "like", "social"})
     */
    private $proposals;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user", "proposal", "review", "comment", "like", "social"})
     * @Assert\NotBlank()
     * @Assert\Length(
     * min = 2,
     * max = 50,
     * minMessage = "Your social network's name must be at least {{ limit }} characters long",
     * maxMessage = "Your social network's name cannot be longer than {{ limit }} characters"
     * )
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Publication", mappedBy="social_network_id", orphanRemoval=true)
     * @Groups({"social"})
     */
    private $publications;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\UploadedDocument", inversedBy="socialNetworks")
     */
    private $uploaded_documents;

    public function __construct()
    {
        $this->user_id = new ArrayCollection();
        $this->proposals = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->uploaded_documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     * @Groups({"social_network:read"})
     */
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(User $userId): self
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id[] = $userId;
        }

        return $this;
    }

    public function removeUserId(User $userId): self
    {
        if ($this->user_id->contains($userId)) {
            $this->user_id->removeElement($userId);
        }

        return $this;
    }

    /**
     * @return Collection|Proposal[]
     * @Groups({"social_network:read"})
     */
    public function getProposals(): Collection
    {
        return $this->proposals;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        if ($this->proposals->contains($proposal)) {
            $this->proposals->removeElement($proposal);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publications->contains($publication)) {
            $this->publications[] = $publication;
            $publication->setSocialNetworkId($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->contains($publication)) {
            $this->publications->removeElement($publication);
            // set the owning side to null (unless already changed)
            if ($publication->getSocialNetworkId() === $this) {
                $publication->setSocialNetworkId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UploadedDocument[]
     */
    public function getUploadedDocuments(): Collection
    {
        return $this->uploaded_documents;
    }

    public function addUploadedDocument(UploadedDocument $uploadedDocument): self
    {
        if (!$this->uploaded_documents->contains($uploadedDocument)) {
            $this->uploaded_documents[] = $uploadedDocument;
        }

        return $this;
    }

    public function removeUploadedDocument(UploadedDocument $uploadedDocument): self
    {
        if ($this->uploaded_documents->contains($uploadedDocument)) {
            $this->uploaded_documents->removeElement($uploadedDocument);
        }

        return $this;
    }
}
