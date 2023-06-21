<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository)
    {
        $category = $categoryRepository->findOneByName($categoryName); //prends le champ Name // $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        if (!$category) 
        {
            throw $this->createNotFoundException(
                '404 - No category with name : ' . $categoryName . ' found in category\'s table.'
            );
        }
        $programsByCategory = $programRepository->findByCategory($category->getId(), ['id' => 'DESC'], 3, 0); //id de la categorie
        return $this->render('category/show.html.twig', ['programsByCategory' => $programsByCategory, 'category' => $category,]);
    }
}
