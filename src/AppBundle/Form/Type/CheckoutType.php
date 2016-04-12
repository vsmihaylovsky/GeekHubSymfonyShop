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

class CheckoutType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                    'attr' => array('placeholder' => 'Enter attribute name')
                )
            )
            ->add('type', ChoiceType::class, array(
                    'choices'           => [
                        'select' => 'select',
                        'text'   => 'text'
                    ],
                    'choices_as_values' => true
                )
            )
            ->add('filterable', ChoiceType::class, array(
                    'choices'           => [
                        'true'   => '1',
                        'false'  => '0'
                    ],
                    'choices_as_values' => true
                )
            )
            ->add('options', CollectionType::class, array(
                'entry_type'    => AttributeOptionType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'label'         => 'Option',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Attribute',
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "attribute";
    }
}