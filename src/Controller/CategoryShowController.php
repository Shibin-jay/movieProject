<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryShowController  extends  AbstractController{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route("/categories", name:"categories_list")]
    public function listCategories(): Response
    {
        $repository = $this->em->getRepository(Category::class);
        $categories =$repository->findAll();
        return $this->render('Customer/Category/index.html.twig',['categories'=>$categories]);
    }

    #[Route("/category/{categoryId}/movies" , name: 'movies_by_category')]
    public function listMoviesByCategory(int $categoryId): Response
    {
        $category = $this->em->getRepository(Category::class)->find($categoryId);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        // Assuming you have a method in the Category entity to get associated movies
        $movies = $category->getMovies();

        return $this->render('Customer/movieList.html.twig', [
            'movies' => $movies,
        ]);
    }

}