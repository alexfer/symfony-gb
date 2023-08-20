<?php

namespace App\Entity;

use App\Repository\GBRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: GBRepository::class)]
class GB
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
//    #[ORM\Column(length: 512)]
//    private ?string $name = null;
//
//    #[ORM\Column(length: 255)]
//    //#[Assert\NotBlank()]
//    //#[Assert\Email()]
//    protected $email;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 65535, type: 'text')]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $uuid = null;

    #[ORM\Column]    
    private ?int $user_id = null;

    //#[Gedmo\Timestampable(on:"update")]
    //#[ORM\Column(type: 'datetime', nullable: true)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private \DateTime $created_at;

    //#[Gedmo\Timestampable(on:"update")]
    //#[ORM\Column(type: 'datetime', nullable: true)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, columnDefinition: 'DATETIME on update CURRENT_TIMESTAMP')]
    private \DateTime $updated_at;
    
    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
        //return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;

        //return $this;
    }
}
