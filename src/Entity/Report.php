<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Publicacion::class)]
    private ?Publicacion $publicacion = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $reporter = null;

    #[ORM\Column(length: 255)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?bool $resolved = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicacion(): ?Publicacion
    {
        return $this->publicacion;
    }

    public function setPublicacion(?Publicacion $publicacion): static
    {
        $this->publicacion = $publicacion;
        return $this;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): static
    {
        $this->reporter = $reporter;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    public function isResolved(): ?bool
    {
        return $this->resolved;
    }

    public function setResolved(bool $resolved): static
    {
        $this->resolved = $resolved;
        return $this;
    }

    // Getters y setters...
} 