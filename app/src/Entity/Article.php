<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[Groups('article:light')]
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

    #[ORM\ManyToOne(inversedBy: 'created_articles')]
    #[Groups('article:full')]
    private ?User $created_by = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('article:light')]
    private ?Flux $belong_to = null;

    #[ORM\OneToMany(mappedBy: 'belong_to_article', targetEntity: Comment::class, orphanRemoval: true, fetch:"LAZY")]
    #[Groups('article:full')]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getBelongTo(): ?Flux
    {
        return $this->belong_to;
    }

    public function setBelongTo(?Flux $belong_to): self
    {
        $this->belong_to = $belong_to;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBelongToArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBelongToArticle() === $this) {
                $comment->setBelongToArticle(null);
            }
        }

        return $this;
    }
}