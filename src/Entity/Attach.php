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
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GB::class)]
    #[ORM\JoinColumn(nullable: false, name: "gb_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?GB $gb = null;

    #[ORM\Column()]
    private ?int $gb_id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    private string $mime;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $size = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private \DateTime $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->size = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getGb(): ?GB
    {
        return $this->gb;
    }

    public function setGB(GB $gb): self
    {
        $this->gb = $gb;

        return $this;
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
