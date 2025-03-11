<?php

namespace App\Domain\Task\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Domain\User\Entity\User;

#[ORM\Entity]
class Task
{
    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "datetime_immutable")]
    #[Assert\NotNull()]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $dueDate = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice([self::STATUS_TODO, self::STATUS_IN_PROGRESS, self::STATUS_DONE])]
    private string $status = self::STATUS_TODO;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "tasks")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Le champ completedAt
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $completedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTime $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    // Ajout des méthodes getCompletedAt et setCompletedAt

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTime $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }
}
