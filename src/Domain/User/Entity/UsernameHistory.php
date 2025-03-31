<?php

namespace App\Domain\User\Entity;

use App\Application\Port\Repository\UsernameHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsernameHistoryRepository::class)]
class UsernameHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Identifiant unique de l'historique

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'usernameHistories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null; // Utilisateur concerné par le changement de pseudo

    #[ORM\Column(length: 255)]
    private ?string $oldPseudo = null; // Ancien pseudo de l'utilisateur

    #[ORM\Column(length: 255)]
    private ?string $newPseudo = null; // Nouveau pseudo choisi

    #[ORM\Column]
    private ?\DateTimeImmutable $changedAt = null; // Date du changement de pseudo

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOldPseudo(): ?string
    {
        return $this->oldPseudo;
    }

    public function setOldPseudo(string $oldPseudo): static
    {
        $this->oldPseudo = $oldPseudo;
        return $this;
    }

    public function getNewPseudo(): ?string
    {
        return $this->newPseudo;
    }

    public function setNewPseudo(string $newPseudo): static
    {
        $this->newPseudo = $newPseudo;
        return $this;
    }

    public function getChangedAt(): ?\DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTimeImmutable $changedAt): static
    {
        $this->changedAt = $changedAt;
        return $this;
    }
}