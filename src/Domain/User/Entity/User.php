<?php

namespace App\Domain\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\User\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Domain\Task\Entity\Task;

#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    private ?string $fullName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\Column]
    private array $roles = [];

    private ?string $plainPassword = null;

    #[ORM\Column]
    private ?string $password = 'password';

    #[ORM\Column]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(options: ["default" => 0])]
    #[Assert\NotNull()]
    #[Assert\PositiveOrZero()]
    private int $exp = 0;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExp(): int
    {
        return $this->exp;
    }

    public function setExp(int $exp): static
    {
        $this->exp = max(0, $exp);
        return $this;
    }

    public function addExp(int $points): static
    {
        $this->exp += max(0, $points);
        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setUser($this);
        }
        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }
        return $this;
    }

    public function getTreeImage(): string
    {
        $levels = [100, 200, 300, 400, 500, 600, 700];
        $imageIndex = 1;

        foreach ($levels as $index => $xpThreshold) {
            if ($this->exp >= $xpThreshold) {
                $imageIndex = $index + 2;
            }
        }

        return "images/{$imageIndex}.png";
    }

    public function getTreeSize(): int
    {
        $levels = [100, 200, 300, 400, 500, 600, 700];
        $sizes = [30, 35, 45, 50, 55, 60, 65];
        $sizeIndex = 0;

        foreach ($levels as $index => $xpThreshold) {
            if ($this->exp >= $xpThreshold) {
                $sizeIndex = $index;
            }
        }

        return $sizes[$sizeIndex] ?? 30;
    }

    public function __toString(): string
    {
        return $this->pseudo ?: $this->fullName;
    }
}
