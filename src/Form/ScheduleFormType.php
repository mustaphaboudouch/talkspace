<?php

namespace App\Form;

use App\Entity\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', null, [
                'attr' => [
                    'class' => 'time-input',
                ],
                'label' => 'De',
            ])
            ->add('endTime', null, [
                'attr' => [
                    'class' => 'time-input',
                ],
                'label' => 'À',
            ])
            ->add('schedule', ChoiceType::class, [
                'attr' => [
                    'class' => 'rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm',
                ],
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
            'data_class' => Schedule::class,
        ]);
    }
}
