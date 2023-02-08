<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'autocomplete' => 'given-name',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prénom.',
                    ]),
                ],
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'autocomplete' => 'family-name',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom.',
                    ]),
                ],
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'autocomplete' => 'email',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse e-mail.',
                    ]),
                ],
                'label' => 'Adresse e-mail',
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre message.',
                    ]),
                ],
                'label' => 'Votre message',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
