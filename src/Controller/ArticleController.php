<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(Article $article, Request $request, EntityManagerInterface $manager)
    {
        /*
         * Sous l'article, si l'utilisateur n'est pas connecté,
         * l'inviter à le faire pour pouvoir écrire un commentaire.
         * Sinon, lui afficher un formulaire avec un textarea
         * pour pouvoir écrire un commentaire.
         * Nécessite une entité Comment avec :
         * - content (text en bdd)
         * - publicationDate (datetime)
         * - user (l'utilisateur qui écrit le commentaire)
         * - article (l'article sur lequel on écrit le commentaire)
         * Nécessite le form type qui va avec contenant le textarea,
         * le contenu du commentaire ne doit pas être vide.
         *
         * Lister les commentaires en dessous, avec nom utilisateur,
         * date de publication, contenu du message
         */
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment
                    ->setUser($this->getUser())
                    ->setArticle($article)
                ;

                $manager->persist($comment);
                $manager->flush();

                $this->addFlash('success', 'Votre commentaire est enregistré');

                // redirection sur la page sur laquel on est, mais en GET et non en POST
                // pour ne pas enregistrer une 2e fois le commentaire en actualisant la page
                return $this->redirectToRoute(
                    'app_article_index',
                    [
                        'id' => $article->getId()
                    ]
                );
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render(
            'article/index.html.twig',
            [
                'article' => $article,
                'form' => $form->createView()
            ]
        );
    }
}
