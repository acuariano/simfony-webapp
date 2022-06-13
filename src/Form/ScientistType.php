<?php

namespace App\Form;

use App\Entity\Scientist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScientistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nationality')
            ->add('Pet')
            ->add('Drink')
            ->add('House')
            ->add('Cigarettes')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scientist::class,
        ]);
    }
}
