<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Form\MovieType;
use App\Form\EditMovieType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
//#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    private $em;
    private $movieRepository;
    public function __construct(EntityManagerInterface $em, MovieRepository $movieRepository)
    {
        $this->em = $em;
        $this->movieRepository = $movieRepository;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function list(): Response
    {
        $repository = $this->em->getRepository(Movie::class);
        $movies =$repository->findAll();

        return $this->render('Admin/list_movies.html.twig', [
            'movies'=> $movies,
        ]);
    }
    #[Route('/create', name: 'dashboard_create_movie')]
    public function createMovie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and movie creation here

            // For example, persist the movie to the database
//            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($movie);
            $entityManager->flush();

            // Redirect to the movie list or any other page
            return $this->redirectToRoute('admin_app_dashboard');
        }

        return $this->render('Admin/create_movie.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/edit/{id}', name: 'dashboard_edit_movie')]
    public function editMovie(Request $request, int $id): Response
    {
        $movie = $this->movieRepository->find($id);

        if (!$movie) {
            throw $this->createNotFoundException('Movie not found');
        }

        $form = $this->createForm(EditMovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Redirect to the movie list or any other page
            return $this->redirectToRoute('admin_app_dashboard');
        }

        return $this->render('Admin/edit_movie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'dashboard_delete_movie')]
    public function deleteMovie(Movie $movie , EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($movie);
        $entityManager->flush();

        // You can add a flash message to indicate successful deletion
        $this->addFlash('success', 'Movie deleted successfully.');

        return $this->redirectToRoute('admin_app_dashboard');
    }
}
