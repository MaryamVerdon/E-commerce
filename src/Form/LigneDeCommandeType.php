<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Article;
use App\Entity\Taille;
use App\Entity\LigneDeCommande;

class LigneDeCommandeType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('qte', IntegerType::class, [
          'attr' => [
            'placeholder' => 1,
          ]
        ])
        ->add('article', EntityType::class,[
          'class' => Article::class,
          'choice_label' => function ($entityType) {
              return $entityType->getLibelle();
          }
        ])
        ->add('taille', EntityType::class,[
          'class' => Taille::class,
          'choice_label' => function ($entityType) {
              return $entityType->getLibelle();
          }
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\LigneDeCommande'
    ));
  }
}
