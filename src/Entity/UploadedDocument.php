<?php

namespace App\Entity;

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
     * @Groups({"proposal"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable = false)
     * @Groups({"proposal"})
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
     * @Groups({"proposal"})
     * @Assert\NotBlank()
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="uploadedDocuments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $data_path;


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

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

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

}
