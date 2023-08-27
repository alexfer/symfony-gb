<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\GB;

#[ORM\Entity]
#[ORM\Table(name: 'comment')]
class Comment
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $author = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'comment.blank')]
    #[Assert\Length(min: 5, minMessage: 'comment.too_short', max: 10000, maxMessage: 'comment.too_long')]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $created_at;

    #[ORM\ManyToOne(targetEntity: GB::class)]
    #[ORM\JoinColumn(nullable: false, name: "gb_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?GB $gb = null;

    #[ORM\Column()]
    private ?int $gb_id = null;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAEmail($email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getGb(): ?GB
    {
        return $this->post;
    }

    public function setGB(GB $gb): void
    {
        $this->gb = $gb;
    }

    public function getGbId(): ?int
    {
        return $this->gb_id;
    }

    public function setUserId(int $gb_id): self
    {
        $this->gb_id = $gb_id;

        return $this;
    }
}
