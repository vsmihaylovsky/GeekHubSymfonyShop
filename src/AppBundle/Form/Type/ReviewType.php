<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/14/16
 * Time: 11:02 PM
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reviewText', TextareaType::class, ['label' => false])
            ->add('rating', NumberType::class, [
                'attr' => [
                    'class' => 'rating',
                    'data-min' => 0,
                    'data-max' => 5,
                    'data-step' => 1,
                    'data-show-clear' => 'false'
                ],
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Review',
        ]);
    }
}