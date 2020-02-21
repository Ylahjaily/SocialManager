<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * 
 * @ApiResource(
 * normalizationContext={"groups"={"user:read"}},
 * denormalizationContext={"groups"={"user:write"}},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ProposalRepository")
 */
class Proposal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user:read"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"user:read"})
     */
    private $textContent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read"})
     */
    private $link;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user:read"})
     */
    private $is_published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:read"})
     */
    private $date_publication_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user:write"})
     */
    private $user_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="proposal_id", orphanRemoval=true)
     * @Groups({"user:read"})
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="proposal_id", orphanRemoval=true)
     * @Groups({"user:read"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="proposal_id", orphanRemoval=true)
     * @Groups({"user:read"})
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SocialNetwork", mappedBy="proposals")
     * @Groups({"user:read"})
     */
    private $socialNetworks;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UploadedDocument", mappedBy="proposal_id", cascade={"persist", "remove"})
     * @Groups({"user:read"})
     */
    private $uploadedDocument;

    public function __construct()
    {
        $this->created_at = new \DateTime('now');
        $this->is_published = false;
        
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->socialNetworks = new ArrayCollection();
    }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(string $textContent): self
    {
        $this->textContent = $textContent;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): self
    {
        $this->is_published = $is_published;

        return $this;
    }

    public function getDatePublicationAt(): ?\DateTimeInterface
    {
        return $this->date_publication_at;
    }

    public function setDatePublicationAt(?\DateTimeInterface $date_publication_at): self
    {
        $this->date_publication_at = $date_publication_at;

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

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProposalId($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getProposalId() === $this) {
                $review->setProposalId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProposalId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getProposalId() === $this) {
                $comment->setProposalId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setProposalId($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getProposalId() === $this) {
                $like->setProposalId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SocialNetwork[]
     */
    public function getSocialNetworks(): Collection
    {
        return $this->socialNetworks;
    }

    public function addSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if (!$this->socialNetworks->contains($socialNetwork)) {
            $this->socialNetworks[] = $socialNetwork;
            $socialNetwork->addProposal($this);
        }

        return $this;
    }

    public function removeSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if ($this->socialNetworks->contains($socialNetwork)) {
            $this->socialNetworks->removeElement($socialNetwork);
            $socialNetwork->removeProposal($this);
        }

        return $this;
    }

    public function getUploadedDocument(): ?UploadedDocument
    {
        return $this->uploadedDocument;
    }

    public function setUploadedDocument(UploadedDocument $uploadedDocument): self
    {
        $this->uploadedDocument = $uploadedDocument;

        // set the owning side of the relation if necessary
        if ($uploadedDocument->getProposalId() !== $this) {
            $uploadedDocument->setProposalId($this);
        }

        return $this;
    }
}
