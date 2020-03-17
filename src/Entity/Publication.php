<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PublicationRepository")
 */
class Publication
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"publication"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Proposal", inversedBy="publications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"publication"})
     */
    private $proposal_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="publications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"publication"})
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SocialNetwork", inversedBy="publications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"publication"})
     */
    private $social_network_id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"publication"})
     * @Assert\NotBlank()
     */
    private $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime('now');
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

    public function getSocialNetworkId(): ?SocialNetwork
    {
        return $this->social_network_id;
    }

    public function setSocialNetworkId(?SocialNetwork $social_network_id): self
    {
        $this->social_network_id = $social_network_id;

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
}
