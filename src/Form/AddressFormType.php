<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class AddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adresse')
            ->add('ville')
            ->add('cp', NumberType::class)
            ->add('pays', ChoiceType::class, [
                                                    'choices' => [
                                                        'France' => 'France',
                                                        'Espagne' => 'Espagne',
                                                        'Allemagne' => 'Allemagne' ,
                                                        'Belgique' => 'Belgique',
                                                        'Italie' => 'Italie'
                                                    ],
                                                ])
            ->add('tel', TelType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
