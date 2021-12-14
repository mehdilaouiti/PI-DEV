<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('type',choiceType::class,[
                'choices'=>[
                    'Défaut de fabrication' =>'Défaut de fabrication',
                    'Probléme de livraison' =>'Probléme de livraison',
                    'Qualité de service'=>'Qualité de service',
                    'Mode de fonctionnement'=>'Mode de fonctionnement',


                ],
                'multiple'=>false,
                'expanded'=>true,
                'label'=>'Type de réclamation'
            ])
            ->add('description',TextareaType::class)
            ->add('img',FileType::class,array('label'=>'inserer votre justificatif','data_class' => null))
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
