<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\ModePaiement;
use App\Entity\StatutCommande;
use App\Entity\Commande;
use App\Form\LigneDeCommandeType;

class CommandeType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('date', DateType::class)
        ->add('mode_paiement', EntityType::class,[
          'class' => ModePaiement::class,
          'choice_label' => function ($entityType) {
              return $entityType->getLibelle();
          }
        ])
        ->add('statut_commande', EntityType::class,[
          'class' => StatutCommande::class,
          'choice_label' => function ($entityType) {
              return $entityType->getLibelle();
          }
        ])
        ->add('lignes_de_commande', CollectionType::class,[
          'entry_type' => LigneDeCommandeType::class,
          'entry_options' => ['label' => false],
          'allow_add' => true,
          'allow_delete' => true,
        ])
        ->add('frais_de_port', NumberType::class, [
          'attr' => [
            'placeholder' => 2.99,
          ]
        ])
        ->add('save', SubmitType::class);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Commande'
    ));
  }
}
