<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
                ])
            ->add('firstName', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
                ])
            ->add('phoneNumber', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
                ])
            ->add('address', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
                ])
            ->add('address2', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
                ])
            ->add('zipcode', TextType::class,[
                'attr' => [
                'class' => 'form-control'
                ]
                ])
            ->add('city', TextType::class,[
                'attr' => [
                'class' => 'form-control'
                ]
                ])
            ->add('status', EntityType::class,[
                'class' => Status::class ,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'labelradio'],
                'expanded' => true,
                

            ])
            // ->add('reset', ResetType::class, [
            //     'attr' => [
            //         'class' => 'reset',
            //         'type' => 'reset'],
            // ]);
            // ->add('updatedAt')
            // ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
