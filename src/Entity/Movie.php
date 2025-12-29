<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $synopsis = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $director = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $actors = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poster = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trailer = null;

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

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): static
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(?string $director): static
    {
        $this->director = $director;

        return $this;
    }

    public function getActors(): ?string
    {
        return $this->actors;
    }

    public function setActors(?string $actors): static
    {
        $this->actors = $actors;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): static
    {
        $this->poster = $poster;

        return $this;
    }

    public function getTrailer(): ?string
    {
        return $this->trailer;
    }

    public function setTrailer(?string $trailer): static
    {
        $this->trailer = $trailer;

        return $this;
    }
}
