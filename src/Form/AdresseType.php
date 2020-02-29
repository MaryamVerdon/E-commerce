<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\AddressType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse', TextType::class, [
                'label_format' => 'Adresse',
            ])
            ->add('ville', TextType::class, [
                'label_format' => 'Ville',
            ])
            ->add('cp', NumberType::class, [
                'label_format' => 'Code Postal',
            ])
            ->add('pays', ChoiceType::class, [
                'label_format' => 'Pays',
                'choices' => [
                    'France' => 'France',
                    'Espagne' => 'Espagne',
                    'Allemagne' => 'Allemagne' ,
                    'Belgique' => 'Belgique',
                    'Italie' => 'Italie'
                ],
            ]) //CountryType
            ->add('tel', TelType::class, [
                'label_format' => 'Téléphone',
                'required' => false,
            ])
            ->add('Ajouter', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
