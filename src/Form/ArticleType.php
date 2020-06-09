<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre'
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Contenu'
                ]
            )
            ->add(
                'category',
                // select sur les valeurs de la table liée à une Entité
                EntityType::class,
                [
                    'label' => 'Catégorie',
                    // classe de l'entité
                    'class' => Category::class,
                    // attribut qui s'affiche dans le select
                    'choice_label' => 'name',
                    // pour avoir une 1ère option vide qui oblige
                    // l'utilisateur à choisir
                    'placeholder' => 'Choisissez une catégorie'
                ]
            )

            // ces 2 champs ne sont pas dans le formulaire,
            // les valeurs seront définies dans le contrôleur
//            ->add('publicationDate')
//            ->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
