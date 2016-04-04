<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeOption;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterOptionsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Attribute $attribute */
            $attribute = $event->getData();
            $form = $event->getForm();

            $form->add('params', EntityType::class, [
                'class'             => 'AppBundle:AttributeOption',
                'choices'           => $attribute->getOptions(),
                'label'             => $attribute->getName(),
                'choice_label'      => 'attributeOption',
                'choices_as_values' => true,
                'expanded'          => true,
                'multiple'          => true,
                'required'          => false,
            ]);
        });
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
        return "filterOptions";
    }
}