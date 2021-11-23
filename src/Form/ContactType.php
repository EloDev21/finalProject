<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname',TextType::class)
            ->add('email',EmailType::class)
            ->add('message',TextareaType::class)
            ->add('subject', ChoiceType::class, [
                'choices' => [
                    'Problème de réservations' => 1,
                    'Informations' => 2,
                    'Remboursement' => 3,
                ],
                'choice_attr' => [
                    'Problème de réservations' => ['data-color' => 'Red'],
                    'Informations' => ['color' => 'Yellow'],
                    'Remboursement' => ['data-color' => 'Green'],
               
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
