<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'UUID')]
    #[ORM\CustomIdGenerator(class: 'Ramsey\Uuid\Doctrine\UuidGenerator')]
    private string $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $pseudo;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Flux::class)]
    private $createdFluxes;

    public function __construct()
    {
        $this->createdFluxes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
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
        return $this->createdFluxes;
    }

    public function addCreatedFlux(Flux $createdFlux): self
    {
        if (!$this->createdFluxes->contains($createdFlux)) {
            $this->createdFluxes[] = $createdFlux;
            $createdFlux->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedFlux(Flux $createdFlux): self
    {
        if ($this->createdFluxes->removeElement($createdFlux)) {
            // set the owning side to null (unless already changed)
            if ($createdFlux->getCreatedBy() === $this) {
                $createdFlux->setCreatedBy(null);
            }
        }

        return $this;
    }
}
