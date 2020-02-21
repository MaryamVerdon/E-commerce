<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\QuantiteTaille;
use App\Entity\Taille;

class QuantiteTailleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('qte', IntegerType::class, [
          'attr' => [
            'placeholder' => 10,
          ]
        ])
        ->add('taille', EntityType::class,[
            'class' => Taille::class,
            'choice_label' => function ($taille) {
                return $taille->getLibelle();
            }
            ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\QuantiteTaille'
    ));
  }
}
