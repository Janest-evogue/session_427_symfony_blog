<?php


namespace App\Controller\Admin;


use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/commentaires")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/article/{id}")
     */
    public function index(Article $article)
    {
        return $this->render(
            'admin/comment/index.html.twig',
            [
                'article' => $article
            ]
        );
    }

    /**
     * @Route("/supression/{id}")
     */
    public function delete(Comment $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash('success', 'Le commentaire est supprimé');

        return $this->redirectToRoute(
            'app_admin_comment_index',
            [
                'id' => $comment->getArticle()->getId()
            ]
        );
    }
}
