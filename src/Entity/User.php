<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user'])]
    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[Groups(['user'])]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Publicacion>
     */
    #[ORM\OneToMany(targetEntity: Publicacion::class, mappedBy: 'userPost')]
    private Collection $publicacions;

    /**
     * @var Collection<int, Comments>
     */
    #[ORM\OneToMany(targetEntity: Comments::class, mappedBy: 'user')]
    private Collection $comments;

    /**
     * @var Collection<int, Followers>
     */
    #[ORM\OneToMany(targetEntity: Followers::class, mappedBy: 'followed')]
    private Collection $followers;

    /**
     * @var Collection<int, Followers>
     */
    #[ORM\OneToMany(targetEntity: Followers::class, mappedBy: 'follower')]
    private Collection $following;

    public function __construct()
    {
        $this->publicacions = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Publicacion>
     */
    public function getPublicacions(): Collection
    {
        return $this->publicacions;
    }

    public function addPublicacion(Publicacion $publicacion): static
    {
        if (!$this->publicacions->contains($publicacion)) {
            $this->publicacions->add($publicacion);
            $publicacion->setUserPost($this);
        }

        return $this;
    }

    public function removePublicacion(Publicacion $publicacion): static
    {
        if ($this->publicacions->removeElement($publicacion)) {
            // set the owning side to null (unless already changed)
            if ($publicacion->getUserPost() === $this) {
                $publicacion->setUserPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Followers>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(Followers $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->addUser($this);
        }

        return $this;
    }

    public function removeFollower(Followers $follower): static
    {
        if ($this->followers->removeElement($follower)) {
            $follower->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Followers>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(Followers $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
            $following->addFollower($this);
        }

        return $this;
    }

    public function removeFollowing(Followers $following): static
    {
        if ($this->following->removeElement($following)) {
            $following->removeFollower($this);
        }

        return $this;
    }
}
