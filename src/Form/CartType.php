<?php

namespace App\Form;

use App\Entity\Cart;
use DateTime;
use Symfony\Component\Form\AbstractType;

// use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('circuit_name', TextType ::class)
            ->add('total' ,IntegerType ::class)
            ->add('firstname')
            ->add('lastname', TextType ::class)
            ->add('created_at', Date::class)
            ->add('date_reservation', TextType:: class)
            ->add('date', DateType::class)
            ->add('rsv', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'empty_data' => '',
             ])
                
            ->add('user_id');
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}
