<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Form\CustomerRatingType;
use App\Repository\RatingRepository;
use App\Repository\MovieRepository;
use App\Form\RatingType;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class MovieController extends AbstractController
{
    private $em;
    private $movieRepository;
    public function __construct(EntityManagerInterface $em, MovieRepository $movieRepository)
    {
        $this->em = $em;
        $this->movieRepository = $movieRepository;
    }

    #[Route('/movies', name: 'app_movies')]
    public function list(): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movies =$repository->findAll();

        return $this->render('Customer/movieList.html.twig', [
            'movies'=> $movies,
        ]);
    }
    #[Route('/movies/{id}', name: 'app_movie_view')]
    public function view(
        Movie $movie,
        RatingRepository $ratingRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory // Inject FormFactoryInterface
    ): Response {
        $ratings = $ratingRepository->findBy(['movie' => $movie]);

        $rating = new Rating();

        // Create the form using the FormFactoryInterface and your form type
        $form = $formFactory->create(CustomerRatingType::class, $rating);

        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the user and movie for the rating
            $rating->setUser($this->getUser());
            $rating->setMovie($movie);

            // Save the rating to the database
            $entityManager->persist($rating);
            $entityManager->flush();

            // Redirect back to the movie view page
            return $this->redirectToRoute('app_movie_view', ['id' => $movie->getId()]);
        }

        return $this->render('Customer/movieView.html.twig', [
            'movie' => $movie,
            'ratings' => $ratings,
            'form' => $form->createView(), // Pass the form to the template
        ]);
    }

}
