<?php

namespace App\Form;

use App\Entity\ContactForm;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('message',TextareaType::class)
            ->add('subject',IntegerType::class)
          
            ->add('sujet', ChoiceType::class, [
                'choices' => [
                    'Problème de réservations' => 'Problème de réservations',
                    'Informations' => 'Informations',
                    'Remboursement' => 'Remboursement',
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
            'data_class' => ContactForm::class,
        ]);
    }
}
