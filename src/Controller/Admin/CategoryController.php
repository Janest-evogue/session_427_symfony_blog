<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(CategoryRepository $repository)
    {
        $categories = $repository->findAll();

        return $this->render(
            'admin/category/index.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * L'id est optionnel parce qu'il a une valeur par défaut
     * Si on ne passe pas d'id on est en création
     * Si on en passe un, on est en modification
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id": "\d+"})
     */
    public function edit(Request $request, EntityManagerInterface $manager, CategoryRepository $repository, $id)
    {
        if (is_null($id)) { // création
            $category = new Category();
        } else { // modification
            $category = $repository->find($id);
        }

        // création du formulaire relié à la catégorie
        $form = $this->createForm(CategoryType::class, $category);

        // le formulaire analyse la requête
        // et sette les valeurs des attribut de l'objet Category
        // avec les valeurs saisies dans le formulaire s'il a été soumis
        $form->handleRequest($request);

        dump($category);

        // si le formulaire a été soumis
        if ($form->isSubmitted()) {
            // si les validations à partir des annotations
            // dans l'entité Category sont ok
            if ($form->isValid()) {
                // enregistrement en bdd par le gestionnaire d'entités
                $manager->persist($category);
                $manager->flush();

                $this->addFlash('success', 'La catégorie est enregistrée');

                return $this->redirectToRoute('app_admin_category_index');
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render(
            'admin/category/edit.html.twig',
            [
                // pour passer le formulaire au template
                'form' => $form->createView()
            ]
        );
    }

    /**
     * Paramconverter : le paramètre typé Category contient un objet Category
     * récupéré automatiquement par un find() sur l'id contenu dans l'url
     *
     * @Route("/suppression/{id}", requirements={"id": "\d+"})
     */
    public function delete(EntityManagerInterface $manager, Category $category)
    {
        // Si la catégorie ne contient aucun article
        if ($category->getArticles()->isEmpty()) {
            // suppression en bdd
            $manager->remove($category);
            $manager->flush();

            $this->addFlash('success', 'La catégorie est supprimée');
        } else {
            $this->addFlash('warning', "La catégorie n'est pas vide");
        }

        return $this->redirectToRoute('app_admin_category_index');
    }
}
