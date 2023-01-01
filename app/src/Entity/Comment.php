<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('comment:light')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 10000,
        maxMessage: 'The content cannot be longer than {{ limit }} characters'
    )]
    #[Groups('comment:light')]
    private ?string $text = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('comment:light')]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups('comment:light')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups('comment:light')]
    private ?\DateTimeImmutable $edited_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups('comment:moderation')]
    private ?\DateTimeImmutable $moderated_at = null;

    #[ORM\ManyToOne(inversedBy: 'written_comments')]
    #[Groups('comment:light')]
    private ?User $written_by = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('comment:full')]
    #[Assert\NotBlank(
        message: 'A comment must be related to an Article'
    )]
    private ?Article $belong_to_article = null;

    #[ORM\ManyToOne]
    #[Groups('comment:light')]
    private ?Comment $parent_comment = null;

    public function __construct()
    {
        $this->childComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

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

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->edited_at;
    }

    public function setEditedAt(?\DateTimeImmutable $edited_at): self
    {
        $this->edited_at = $edited_at;

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

    public function getWrittenBy(): ?User
    {
        return $this->written_by;
    }

    public function setWrittenBy(?User $written_by): self
    {
        $this->written_by = $written_by;

        return $this;
    }

    public function getBelongToArticle(): ?Article
    {
        return $this->belong_to_article;
    }

    public function setBelongToArticle(?Article $belong_to_article): self
    {
        $this->belong_to_article = $belong_to_article;

        return $this;
    }

    public function getParentComment(): ?Comment
    {
        return $this->parent_comment;
    }

    public function setParentComment(?Comment $parent_comment): self
    {
        $this->parent_comment = $parent_comment;

        return $this;
    }
}