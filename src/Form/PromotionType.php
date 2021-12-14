<?php

namespace App\Form;
use App\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use App\Entity\Promotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nompromotion', TextType::class)
            ->add('remise',ChoiceType::class, [
                'choices' => [
                    '5%'=>'0.05',
                    '10%'=>'0.1',
                    '15%'=>'0.15',
                    '20%'=>'0.2',
                    '25%'=>'0.25',
                    '30%'=>'0.3',
                    '35%'=>'0.35',
                    '40%'=>'0.4',
                    '45%'=>'0.45',
                    '50%'=>'0.5',
                    '55%'=>'0.55',
                    '60%'=>'0.6',
                    '65%'=>'0.65',
                    '70%'=>'0.7',
                    '75%'=>'0.75',
                    '80%'=>'0.8',
                    '85%'=>'0.85',
                    '90%'=>'0.9',
                    '95%'=>'0.95',
                ]])
            ->add('description',TextareaType::class)
            ->add('date')
            ->add('articles',EntityType::class,[
                'class'=> Article::class,
                'multiple'=>true,
                'expanded'=>true,
            ])
            ->add('img',FileType::class,array('label'=>'inserer une image','data_class' => null))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
