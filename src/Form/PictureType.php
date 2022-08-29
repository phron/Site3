<?php

namespace App\Form;

use App\Entity\Picture;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PictureType extends AbstractType
{
     
    public function buildForm(FormBuilderInterface $builder, array $options, ): void        
    {
        // en création on ne montre que le champ d'upload
        if($options['add'])
        {
            $builder
                ->add('pictureFile', FileType::class,[
                    'required'=>false,
                    'multiple'=>true,
                    'mapped' => false,
                    'attr' => [
                        // 'onchange'=>'loadFile(event)',
                        'class'=>'form-control'
                    ]
                
                ]);
        }
        
        // en édition on ne montre que les champs éditable (altText, width, height, legend)
        if($options['edit'])
        {
           $builder
            ->add('altText') 
            ;
        }
    }
        

    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            'add' => false,
            'edit' => false
        ]);
    }
}
