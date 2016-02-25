<?php
namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                    'attr' => array('placeholder' => 'Enter category title')
                )
            )
            ->add('parent', EntityType::class, array(
                'class' => 'AppBundle:Category',
                'choice_label'  => 'title',
                'placeholder'   => 'without parent category',
                'empty_data'    => null,
                'required'      => false,
            ))
            ->add('attributes', EntityType::class, array(
                'class'         => 'AppBundle:Attribute',
                'choice_label'  => 'name',
                'placeholder'   => 'Choose attributes',
                'multiple'      => 'true',
                'required'      => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Category',
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "category";
    }
}