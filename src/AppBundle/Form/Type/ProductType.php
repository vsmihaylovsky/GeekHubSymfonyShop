<?php
namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                    'attr' => array('placeholder' => 'Enter product name')
                )
            )
            ->add('category', EntityType::class, array(
                'class'         => 'AppBundle:Category',
                'choice_label'  => 'title',
                'placeholder'   => 'Choose category',
                'required'      => true,
            ))
            ->add('description', TextareaType::class, array(
                    'attr' => array('placeholder' => 'Description')
            ))
            ->add('price', MoneyType::class, array(
                'currency'      => 'USD'
            ))
            ->add('priceSpecial', MoneyType::class, array(
                'currency'      => 'USD'
            ))
            ->add('qty', IntegerType::class
            )
            ->add('available', ChoiceType::class, array(
                    'choices'           => [
                        'true'   => '1',
                        'false'  => '0'
                    ],
                    'choices_as_values' => true
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Product',
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "product";
    }
}