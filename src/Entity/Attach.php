<?php

namespace App\Entity;

use App\Repository\AttachRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: AttachRepository::class)]
#[ORM\Table(name: 'file')]
class Attach
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GB::class)]
    #[ORM\JoinColumn(nullable: false, name: "gb_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?GB $gb = null;

    #[ORM\Column()]
    private ?int $gb_id = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private \DateTime $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNname(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGb(): ?GB
    {
        return $this->gb;
    }

    public function setGB(GB $gb): void
    {
        $this->gb = $gb;
    }

    public function getGbId(): ?int
    {
        return $this->gb_id;
    }

    public function setGbId(int $gb_id): self
    {
        $this->gb_id = $gb_id;

        return $this;
    }
}
