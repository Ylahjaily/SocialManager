<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UploadedDocumentRepository")
 */
class UploadedDocument
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable = false)
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     * @Assert\NotBlank()
     * @Assert\Length(
     * min = 2,
     * max = 30,
     * minMessage = "Your document's name must be at least {{ limit }} characters long",
     * maxMessage = "Your document's name cannot be longer than {{ limit }} characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     * @Assert\NotBlank()
     */
    private $data;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     */
    private $data_path;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     */
    private $is_published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     */
    private $date_publication_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="uploaded_document_id")
     * @Groups({"proposal", "uploadedDoc", "social"})
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="uploaded_document_id")
     * @Groups({"proposal", "uploadedDoc", "social"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="uploaded_document_id")
     * @Groups({"proposal", "uploadedDoc", "social"})
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SocialNetwork", mappedBy="uploaded_documents")
     * @Groups({"proposal", "uploadedDoc", "social"})
     */
    private $socialNetworks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Publication", mappedBy="uploaded_document_id")
     * @Groups({"proposal", "uploadedDoc", "social"})
     */
    private $publications;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="uploadedDocuments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"proposal", "uploadedDoc", "review", "social", "publication"})
     */
    private $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime('now');
        $this->is_published = false;

        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->socialNetworks = new ArrayCollection();
        $this->publications = new ArrayCollection();
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

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }


    public function getDataPath(): ?string
    {
        return $this->data_path;
    }

    public function setDataPath(string $data_path): self
    {
        $this->data_path = $data_path;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(?bool $is_published): self
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
            $review->setUploadedDocumentId($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getUploadedDocumentId() === $this) {
                $review->setUploadedDocumentId(null);
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
            $comment->setUploadedDocumentId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUploadedDocumentId() === $this) {
                $comment->setUploadedDocumentId(null);
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
            $like->setUploadedDocumentId($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getUploadedDocumentId() === $this) {
                $like->setUploadedDocumentId(null);
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
            $socialNetwork->addUploadedDocument($this);
        }

        return $this;
    }

    public function removeSocialNetwork(SocialNetwork $socialNetwork): self
    {
        if ($this->socialNetworks->contains($socialNetwork)) {
            $this->socialNetworks->removeElement($socialNetwork);
            $socialNetwork->removeUploadedDocument($this);
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
            $publication->setUploadedDocumentId($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->contains($publication)) {
            $this->publications->removeElement($publication);
            // set the owning side to null (unless already changed)
            if ($publication->getUploadedDocumentId() === $this) {
                $publication->setUploadedDocumentId(null);
            }
        }

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

}
