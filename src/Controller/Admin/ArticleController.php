<?php


namespace App\Controller\Admin;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(ArticleRepository $repository, Request $request)
    {
        $searchForm = $this->createForm(SearchArticleType::class);

        $searchForm->handleRequest($request);

        dump($searchForm->getData());

        // tous les articles triés par date de publication décroissante
        //$articles = $repository->findBy([], ['publicationDate' => 'DESC']);

        // (array) pour forcer le typage et passer un tableau vide
        // au lieu de null quand il n'y a pas de recherche
        $articles = $repository->search((array)$searchForm->getData());

        return $this->render(
            'admin/article/index.html.twig',
            [
                'articles' => $articles,
                'search_form' => $searchForm->createView()
            ]
        );
    }

    /**
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id": "\d+"})
     */
    public function edit(Request $request, EntityManagerInterface $manager, $id)
    {
        /*
         * Intégrer le formulaire pour l'enregistrement d'un article
         * Validation : tous les champs obligatoires
         * Avant l'enregistrement setter la date de publication à maintenant
         * et l'auteur avec l'utilisateur connecté ($this->getUser() dans un contrôleur)
         *
         * Adapter la page pour la modification :
         * - pas de modification de la date de publication ni de l'auteur
         */
        $originalImage = null;
        $user = $this->getUser();

        if (is_null($id)) { // création
            $article = new Article();
            $article
                ->setAuthor($user)
                // n'est plus utile car fait par le constructeur de la classe Article
                //->setPublicationDate(new \DateTime())
            ;
        } else { // modification
            // même chose que d'appeler la méthode find() d'ArticleRepository
            $article = $manager->find(Article::class, $id);

            if (!is_null($article->getImage())) {
                // nom du fichier venant de la bdd
                $originalImage = $article->getImage();

                // le champ de formulaire attend un objet File
                // utilisant le chemin vers le fichier
                $article->setImage(
                    new File($this->getParameter('upload_dir') . $article->getImage())
                );
            }
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var UploadedFile|null $image */
                $image = $article->getImage();

                // si une image a été uploadée
                if (!is_null($image)) {
                    // nom sous lequel on va enregistrer l'image
                    $filename = uniqid() . '.' . $image->guessExtension();

                    // déplacement de l'image uploadée
                    $image->move(
                        // dans quel répertoire
                        // cf config/services.yaml
                        $this->getParameter('upload_dir'),
                        // nom du fichier
                        $filename
                    );

                    // pour enregistrer le nom du fichier en bdd
                    $article->setImage($filename);

                    // en modification, suppression de l'ancienne image si l'article en avait une
                    if (!is_null($originalImage)) {
                        unlink($this->getParameter('upload_dir') . $originalImage);
                    }
                } else {
                    // en modification, sans upload,
                    // on remets le nom de l'image venant de la bdd
                    $article->setImage($originalImage);
                }

                $manager->persist($article);
                $manager->flush();

                $this->addFlash('success', "L'article est enregistré");

                return $this->redirectToRoute('app_admin_article_index');
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render(
            'admin/article/edit.html.twig',
            [
                'form' => $form->createView(),
                'original_image' => $originalImage
            ]
        );
    }

    /**
     * @Route("/suppression/{id}", requirements={"id": "\d+"})
     */
    public function delete(Article $article, EntityManagerInterface $manager)
    {
        // suppression de l'image de l'article s'il en a une
        if (!is_null($article->getImage())) {
            $image = $this->getParameter('upload_dir') . $article->getImage();
            unlink($image);
        }

        // suppression en bdd
        $manager->remove($article);
        $manager->flush();

        $this->addFlash('success', "L'article est supprimé");

        return $this->redirectToRoute('app_admin_article_index');
    }

    /**
     * @Route("/ajax-content/{id}")
     */
    public function ajaxContent(Article $article)
    {
        return new Response(nl2br($article->getContent()));
    }
}
