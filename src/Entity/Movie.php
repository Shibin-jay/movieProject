<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use App\Entity\Rating;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Movie
 * @package APP\Entity
 */

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ORM\Table(name: "Movies")]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ? int $id= null;

    #[ORM\Column(type:"string",length:255)]
    private string $title;
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $releaseDate = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: 'string',length: 255)]
    private string $genre;
    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string',length: 255)]
    private string $imagePath;

    #[ORM\ManyToOne(targetEntity: Category::class,inversedBy: 'movies')]
    private Category $category;

    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'movie')]
    private Collection $ratings;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }


    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setMovie($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getMovie() === $this) {
                $rating->setMovie(null);
            }
        }

        return $this;
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setReleaseDate(\DateTime $releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }

    public function getReleaseDate(): \DateTime
    {
        return $this->releaseDate;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
    public function getCategoryName(): string
    {
        return $this->category->getName();
    }

}
