<?php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route("/admin/categories", name:"admin_categories_list")]
    public function listCategories(): Response
    {
        $repository = $this->em->getRepository(Category::class);
        $categories =$repository->findAll();
        return $this->render('Admin/Category/index.html.twig',['categories'=>$categories]);
    }

    #[Route("/admin/categories/create", name:"admin_category_create")]
    public function createCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();

        // Create a form for creating a new category
        $form = $this->createForm(CategoryType::class, $category);

        // Handle form submissions
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the new category to the database
            $entityManager->persist($category);
            $entityManager->flush();

            // Optionally, add a success flash message
            $this->addFlash('success', 'Category created successfully.');

            // Redirect to a page (e.g., the category list page)
            return $this->redirectToRoute('admin_categories_list');
        }

        return $this->render('Admin/Category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route("/admin/categories/{id}/edit", name:"admin_categories_edit")]
    public function editCategory(Request $request, int $id): Response
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Movie not found');
        }
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and updating the category entity

            $this->em->flush();

            // Redirect to a success page or list of categories
            return $this->redirectToRoute('admin_categories_list');
        }

        return $this->render('Admin/Category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/delete/{id}', name: 'admin_categories_delete')]
    public function deleteMovie(Category $category , EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        // You can add a flash message to indicate successful deletion
        $this->addFlash('success', 'Movie deleted successfully.');

        return $this->redirectToRoute('admin_categories_list');
    }

}
