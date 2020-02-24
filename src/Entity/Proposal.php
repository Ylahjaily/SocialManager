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
 * normalizationContext={"groups"={"proposal:read"}},
 * denormalizationContext={"groups"={"proposal:write"}},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ProposalRepository")
 */
class Proposal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"proposal:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"proposal:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"proposal:read"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"proposal:read"})
     */
    private $textContent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"proposal:read"})
     */
    private $link;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"proposal:read"})
     */
    private $is_published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"proposal:read"})
     */
    private $date_publication_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"proposal:read"})
     */
    private $user_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="proposal_id", orphanRemoval=true)
     * @Groups({"proposal:read","review:read"})
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="proposal_id", orphanRemoval=true)
     * @Groups({"proposal:read"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="proposal_id", orphanRemoval=true)
     * @Groups({"proposal:read"})
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SocialNetwork", mappedBy="proposals")
     * @Groups({"proposal:read"})
     */
    private $socialNetworks;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UploadedDocument", mappedBy="proposal_id", cascade={"persist", "remove"})
     * @Groups({"proposal:read"})
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

    /**
     * @Groups({"user:read"})
     */
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

    /**
     * @Groups({"user:read"})
     */
    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): self
    {
        $this->is_published = $is_published;

        return $this;
    }

    /**
     * @Groups({"user:read"})
     */
    public function getDatePublicationAt(): ?\DateTimeInterface
    {
        return $this->date_publication_at;
    }

    public function setDatePublicationAt(?\DateTimeInterface $date_publication_at): self
    {
        $this->date_publication_at = $date_publication_at;

        return $this;
    }

    /**
     * @Groups({"user:read"})
     */
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

    /**
     * @Groups({"user:read"})
     */
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
