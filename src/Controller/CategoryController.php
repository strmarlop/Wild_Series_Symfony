<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        // Create a new Category Object
        $category = new Category();
        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            $categoryRepository->save($category, true);

            // Redirect to categories list
            return $this->redirectToRoute('category_index');
        }

        // Render the form
        return $this->render('category/new.html.twig', ['form' => $form,]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository)
    {
        $category = $categoryRepository->findOneByName($categoryName); //prends le champ Name // $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        if (!$category) {
            throw $this->createNotFoundException(
                '404 - No category with name : ' . $categoryName . ' found in category\'s table.'
            );
        }
        $programsByCategory = $programRepository->findByCategory($category->getId(), ['id' => 'DESC'], 3, 0); //id de la categorie
        return $this->render('category/show.html.twig', ['programsByCategory' => $programsByCategory, 'category' => $category,]);
    }
}
