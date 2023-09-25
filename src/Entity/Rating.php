<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;




#[ORM\Entity(repositoryClass : MovieRepository::class)]
#[ORM\Table(name: "Rating")]
class Rating{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ? int $id= null;

    #[ORM\Column(type: 'integer')]
    #[Assert\LessThanOrEqual(value: 10, message:"Rating score must be less than or equal to 10.")]
    private ? int $ratingScore= null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $review = null;

    #[ORM\ManyToOne(inversedBy: 'ratings', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user ;

    #[ORM\ManyToOne(inversedBy: 'ratings', targetEntity: Movie::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Movie $movie ;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovie(): Movie
    {
        return $this->movie;
    }

    public function setMovie(Movie $movie): void
    {
        $this->movie = $movie;
    }

    public function getRatingScore(): ?int
    {
        return $this->ratingScore;
    }

    public function setRatingScore(?int $rating): void
    {
        $this->ratingScore = $rating;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): void
    {
        $this->review = $review;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }


}