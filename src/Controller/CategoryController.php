<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(Category $category)
    {
        return $this->render(
            'category/index.html.twig',
            [
                'category' => $category,
            ]
        );
    }

    public function menu(CategoryRepository $repository)
    {
        // pour faire un findAll() avec la possibilité d'ajouter un ORDER BY :
        $categories = $repository->findBy([], ['id' => 'ASC']);

        return $this->render(
            'category/menu.html.twig',
            [
                'categories' => $categories
            ]
        );
    }
}
