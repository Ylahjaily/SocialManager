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
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SocialNetwork", inversedBy="publications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $social_network_id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $consumer_key = "WH59v34LBu5X8jQCcptQB2tPR";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $consumer_secret = "nrg6mKcjAK7ewsq6IGvOPGXYeG5tI3Q6L7ZNXL9EzlxoyW77Dt";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $access_token = "1232322272419090432-73tUP5xyQ7A48Gqodfqa6miJApbMEI";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $access_token_secret = "EvxwRqqxsjWU7E2TBPxddlgeoQflGCsx413d26P5GX4Xn";

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

    public function getConsumerKey(): ?string
    {
        return $this->consumer_key;
    }

    public function setConsumerKey(string $consumer_key): self
    {
        $this->consumer_key = $consumer_key;

        return $this;
    }

    public function getConsumerSecret(): ?string
    {
        return $this->consumer_secret;
    }

    public function setConsumerSecret(string $consumer_secret): self
    {
        $this->consumer_secret = $consumer_secret;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(string $access_token): self
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getAccessTokenSecret(): ?string
    {
        return $this->access_token_secret;
    }

    public function setAccessTokenSecret(string $access_token_secret): self
    {
        $this->access_token_secret = $access_token_secret;

        return $this;
    }
}
