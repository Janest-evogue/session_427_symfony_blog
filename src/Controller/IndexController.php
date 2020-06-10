<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        /**
         * Faire une page article dans un contrôleur ArticleController
         * qui affiche toutes les infos de l'article et l'image s'il y en a une
         *
         * Dans cette page, lister les 3 derniers articles en date avec un lien vers
         * la page article
         *
         * Dans la page d'accueil des catégories, lister les 3 derniers articles
         * de la catégorie
         */

        return $this->render('index/index.html.twig');
    }
}
