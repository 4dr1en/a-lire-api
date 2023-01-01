<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups('user:light')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Length(
        min: 3,
        max: 180,
        minMessage: 'Your username must be at least {{ limit }} characters long',
        maxMessage: 'Your username cannot be longer than {{ limit }} characters'
    )]
    #[Groups('user:light')]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups('user:full')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email]
    #[Groups('user:light')]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Flux::class, fetch:"LAZY")]
    #[Groups('user:full')]
    private Collection $created_fluxes;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: Article::class, fetch:"LAZY")]
    #[Groups('user:articles')]
    private Collection $createdArticles;

    #[ORM\OneToMany(mappedBy: 'written_by', targetEntity: Comment::class, fetch:"LAZY")]
    #[Groups('user:comments')]
    private Collection $writtenComments;

    public function __construct()
    {
        $this->created_fluxes = new ArrayCollection();
        $this->createdArticles = new ArrayCollection();
        $this->writtenComments = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Flux>
     */
    public function getCreatedFluxes(): Collection
    {
        return $this->created_fluxes;
    }

    public function addCreatedFlux(Flux $createdFlux): self
    {
        if (!$this->created_fluxes->contains($createdFlux)) {
            $this->created_fluxes->add($createdFlux);
            $createdFlux->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedFlux(Flux $createdFlux): self
    {
        if ($this->created_fluxes->removeElement($createdFlux)) {
            // set the owning side to null (unless already changed)
            if ($createdFlux->getCreatedBy() === $this) {
                $createdFlux->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getCreatedArticles(): Collection
    {
        return $this->createdArticles;
    }

    public function addCreatedArticle(Article $createdArticle): self
    {
        if (!$this->createdArticles->contains($createdArticle)) {
            $this->createdArticles->add($createdArticle);
            $createdArticle->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedArticle(Article $createdArticle): self
    {
        if ($this->createdArticles->removeElement($createdArticle)) {
            // set the owning side to null (unless already changed)
            if ($createdArticle->getCreatedBy() === $this) {
                $createdArticle->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getWrittenComments(): Collection
    {
        return $this->writtenComments;
    }

    public function addWrittenComment(Comment $writtenComment): self
    {
        if (!$this->writtenComments->contains($writtenComment)) {
            $this->writtenComments->add($writtenComment);
            $writtenComment->setWrittenBy($this);
        }

        return $this;
    }

    public function removeWrittenComment(Comment $writtenComment): self
    {
        if ($this->writtenComments->removeElement($writtenComment)) {
            // set the owning side to null (unless already changed)
            if ($writtenComment->getWrittenBy() === $this) {
                $writtenComment->setWrittenBy(null);
            }
        }

        return $this;
    }
}