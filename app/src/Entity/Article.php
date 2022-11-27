<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('article:light')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The title cannot be longer than {{ limit }} characters'
    )]
    #[Assert\Url]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 180,
        maxMessage: 'The title cannot be longer than {{ limit }} characters',
        min: 2,
        minMessage: 'The title must be at least {{ limit }} characters long'
    )]
    #[Groups('article:light')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 10000,
        maxMessage: 'The content cannot be longer than {{ limit }} characters'
    )]
    #[Groups('article:full')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('article:light')]
    private ?string $thumbnail = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('article:light')]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups('article:light')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups('article:full')]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups('article:full')]
    private ?\DateTimeImmutable $moderated_at = null;

    #[ORM\ManyToOne(inversedBy: 'createdArticles')]
    #[Groups('article:full')]
    private ?user $created_by = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    //#[Assert\Valid]
    #[Groups('article:light')]
    private ?flux $belongTo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getModeratedAt(): ?\DateTimeImmutable
    {
        return $this->moderated_at;
    }

    public function setModeratedAt(?\DateTimeImmutable $moderated_at): self
    {
        $this->moderated_at = $moderated_at;

        return $this;
    }

    public function getCreatedBy(): ?user
    {
        return $this->created_by;
    }

    public function setCreatedBy(?user $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getBelongTo(): ?flux
    {
        return $this->belongTo;
    }

    public function setBelongTo(?flux $belongTo): self
    {
        $this->belongTo = $belongTo;

        return $this;
    }
}