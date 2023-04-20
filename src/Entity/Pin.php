<?php

namespace App\Entity;

use App\Repository\PinRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PinRepository::class)]
#[ORM\Table(name:"pins")]
#[ORM\HasLifecycleCallbacks]
class Pin
{
    use Timestampable;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:3, minMessage:"Le titre doit contenir au moins 3 caractères.")]
    #[Assert\NotBlank(message:"Le titre ne doit pas être vide.")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:10, minMessage:"La description doit contenir au moins 10 caractères.")]
    #[Assert\NotBlank(message:"La description ne doit pas être vide.")]
    private ?string $description = null;

    #[ORM\Column(type:"datetime_immutable", options:["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type:"datetime_immutable", options:["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamp(){
        if($this->getCreatedAt() === null){
            $this->setCreatedAt(new \DateTimeImmutable);
        }
        $this->setUpdatedAt(new \DateTimeImmutable);
    }
}
