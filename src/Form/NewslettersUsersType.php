<?php

namespace App\Form;

use App\Entity\Newsletters\Categories;
use App\Entity\Newsletters\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class NewslettersUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // enitytype car les categories vont nous venir de l'entité categoriezs
        ->add('categories', EntityType::class,[
            "class" => Categories::class,
            "choice_label" => 'name',
            "multiple" => true,
            "expanded" => true,
// pasmultiple mais expanded bouton radio et le contraire liste deroulante les 2 cases a cocher
        ])
            ->add('email',EmailType::class)
           
            ->add('is_confirmed',CheckboxType::class,[
                "constraints" => [
                    // on oblige la personne a cocher
                   new  IsTrue([
                        "message" => "Vous devez acce^peter la collecte de vos données personnelles."

                    ])
                   ],
                   "label" =>'J\'accepte la collecte des données personnelles.'
            ])
            ->add('Envoyer',SubmitType::class)
           
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
