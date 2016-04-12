<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureIsMainType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isMain', ChoiceType::class, [
                    'choices'           => ['1'],
                    'choices_as_values' => true,
                    'expanded'          => true,
                    'multiple'          => false,
                    'label'             => false,
                    'empty_data'        => null,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\ProductPicture',
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "productPictureIsMain";
    }
}