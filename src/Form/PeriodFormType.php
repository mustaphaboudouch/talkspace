<?php

namespace App\Form;

use App\Entity\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime')
            ->add('endTime')
            ->add('schedule', ChoiceType::class, [
                'choices' => [
                    'Lundi' => 'MONDAY',
                    'Mardi' => 'TUESDAY',
                    'Mercredi' => 'WEDNESDAY',
                    'Jeudi' => 'THURSDAY',
                    'Vendredi' => 'FRIDAY',
                    'Samedi' => 'SATURDAY',
                    'Dimanche' => 'SUNDAY',
                ],
                'label' => 'Jour de la semaine',
                'multiple' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Period::class,
        ]);
    }
}
