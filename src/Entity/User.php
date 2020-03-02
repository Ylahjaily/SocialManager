<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User implements Userinterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="simple_array")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Proposal", mappedBy="user_id", orphanRemoval=true)
     * @Groups({"user", "like", "reviewComment"})
     */
    private $proposals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="user_id", orphanRemoval=true)
     * @Groups({"user","like"})
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $apiKey;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user_id", orphanRemoval=true)
     * @Groups({"user", "like", "reviewComment"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="user_id", orphanRemoval=true)
     * @Groups({"user", "comment", "reviewComment"})
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SocialNetwork", mappedBy="user_id")
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $socialNetworks;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReviewComment", mappedBy="user_id", orphanRemoval=true)
     * @Groups({"user", "comment", "like"})
     */
    private $reviewComments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Publication", mappedBy="user_id", orphanRemoval=true)
     * @Groups({"user", "proposal", "review", "comment", "like", "reviewComment"})
     */
    private $publications;

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->created_at = new \DateTime('now');

        $this->proposals = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->socialNetworks = new ArrayCollection();
        $this->reviewComments = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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

    /**
     * @return Collection|Proposal[]
     */
    public function getProposals(): Collection
    {
        return $this->proposals;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
            $proposal->setUserId($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        if ($this->proposals->contains($proposal)) {
            $this->proposals->removeElement($proposal);
            // set the owning side to null (unless already changed)
            if ($proposal->getUserId() === $this) {
                $proposal->setUserId(null);
            }
        }

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
            $review->setUserId($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getUserId() === $this) {
                $review->setUserId(null);
            }
        }

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

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
            $comment->setUserId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUserId() === $this) {
                $comment->setUserId(null);
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
            $like->setUserId($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getUserId() === $this) {
                $like->setUserId(null);
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
            $socialNetwork->addUserId($this);
        }

        return $this;
    }

    public function removeSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if ($this->socialNetworks->contains($socialNetwork)) {
            $this->socialNetworks->removeElement($socialNetwork);
            $socialNetwork->removeUserId($this);
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
    */
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
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
            $reviewComment->setUserId($this);
        }

        return $this;
    }

    public function removeReviewComment(ReviewComment $reviewComment): self
    {
        if ($this->reviewComments->contains($reviewComment)) {
            $this->reviewComments->removeElement($reviewComment);
            // set the owning side to null (unless already changed)
            if ($reviewComment->getUserId() === $this) {
                $reviewComment->setUserId(null);
            }
        }

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
            $publication->setUserId($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->contains($publication)) {
            $this->publications->removeElement($publication);
            // set the owning side to null (unless already changed)
            if ($publication->getUserId() === $this) {
                $publication->setUserId(null);
            }
        }

        return $this;
    }
}
