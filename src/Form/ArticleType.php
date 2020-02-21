<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Section;
use App\Entity\QuantiteTaille;
use App\Entity\TypeArticle;
use App\Form\QuantiteTailleType;

class ArticleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('libelle', TextType::class)
        ->add('description', TextType::class)
        ->add('prix_u', NumberType::class)
        ->add('image', TextType::class)
        ->add('type_article', EntityType::class,[
            'class' => TypeArticle::class,
            'choice_label' => function ($entityType) {
                return $entityType->getLibelle();
            }
            ])
        ->add('sections', EntityType::class,[
            'class' => Section::class,
            'multiple' => true,
            'choice_label' => function ($section) {
                return $section->getLibelle();
            }
            ])
        ->add('quantite_tailles', CollectionType::class,[
            'entry_type' => QuantiteTailleType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            ])
        ->add('save', SubmitType::class);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Article'
    ));
  }
}
