<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Salle::class, inversedBy: 'films')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Salle $salle = null;  // Corrected property name from $film to $salle

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'films')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
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

    public function getSalle(): ?Salle  // Corrected method name from getFilm to getSalle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static  // Corrected method name from setFilm to setSalle
    {
        $this->salle = $salle;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
        return $this;
    }
}
