<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Repository\RatingRepository;
use App\Form\RatingType;
use App\Form\CustomerRatingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/**
 * @Route("/admin/ratings")
 */
class RatingController extends AbstractController
{
    private $entityManager;

    private $ratingRepository;
    public function __construct(EntityManagerInterface $entityManager, RatingRepository $ratingRepository)
    {
        $this->entityManager = $entityManager;
        $this->ratingRepository = $ratingRepository;

    }

    #[Route("/admin/ratings", name:"admin_ratings_list")]
    public function listRatings( RatingRepository $ratingRepository) : Response
    {
        $ratings= $ratingRepository->findAll();
//        dd($ratings);
        return $this->render('Admin/rating/index.html.twig', [
            'ratings' => $ratings,
        ]);
    }
    #[Route("/admin/ratings/create", name: "admin_rating_create")]
    public function createRating(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rating);
            $entityManager->flush();

            return $this->redirectToRoute('admin_ratings_list');
        }

        return $this->render('Admin/rating/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/rating/edit/{id}", name: "admin_rating_edit")]
    public function edit(Request $request, int $id): Response
    {
        $rating = $this->ratingRepository->find($id);

        if (!$rating) {
            throw $this->createNotFoundException('Rating not found');
        }

//        if ($rating->getUser() !== $this->getUser()) {
//            throw $this->createAccessDeniedException('You are not allowed to edit this rating');
//        }

        $form = $this->createForm(RatingType::class, $rating);

        // Handle form submissions
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the edited rating to the database
            $this->entityManager->flush();

            // Redirect to a success page or wherever you want
            return $this->redirectToRoute('admin_ratings_list');
        }

        return $this->render('Admin/rating/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('admin/rating/delete/{id}', name: 'dashboard_delete_movie')]
    public function deleteMovie(Rating $rating , EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($rating);
        $entityManager->flush();

        // You can add a flash message to indicate successful deletion
        $this->addFlash('success', 'Movie deleted successfully.');

        return $this->redirectToRoute('admin_ratings_list');
    }

//    #[Route('/movies/{id}/rate', name: 'app_movie_rate')]
//    public function rateMovie(Request $request, Movie $movie, EntityManagerInterface $entityManager): Response
//    {
//        $rating = new Rating();
//
//        // Create the form without user and movie fields
//        $form = $this->createForm(CustomerRatingType::class, $rating); // Create the form only once
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            // Set the user and movie for the rating
//            $rating->setUser($this->getUser());
//            $rating->setMovie($movie);
//
//            // Save the rating to the database
//            $entityManager->persist($rating);
//            $entityManager->flush();
//
//            // Redirect back to the movie view page
//            return $this->redirectToRoute('app_movie_view', ['id' => $movie->getId()]);
//        }
//
//        // If the form is not submitted or not valid, or there are errors, you can handle that here
//
//        return $this->render('Customer/movieView.html.twig', [
//            'movie' => $movie,
//            'ratings' => $movie->getRatings(),
//            'form' => $form->createView(), // Pass the form to the template
//        ]);
//    }


    #[Route('/movies/{id}/rate', name: 'app_movie_rate')]
    public function rateMovie(
        Request $request,
        Movie $movie,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        RatingRepository $ratingRepository
    ): Response {
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
        $ratings = $ratingRepository->findBy(['movie' => $movie]);


        // Render the form for rating
        return $this->render('Customer/movieView.html.twig', [
            'movie' => $movie,
            'ratings' => $ratings,
            'form' => $form->createView(),
        ]);
    }



}
