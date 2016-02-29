<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeOption;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAttributesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $eventData = $event->getData();
            /** @var Attribute $attribute */
            $attribute = $eventData->getAttribute();
            $options = $attribute->getOptions();
            $form = $event->getForm();

            if ($attribute->getType() == 'select') {
                $form->add('attributeValue', ChoiceType::class, array(
                    'choices'           => $options,
                    'preferred_choices' => [$eventData->getAttributeOption()],
                    'choices_as_values' => true,
                    'choice_label'      => 'attributeOption',
                    'label'             => $attribute->getName(),
                    'placeholder'       => 'choose value',
                    ));
            }
            else {
                $form->add('attributeValue', TextType::class, [
                    'label'             => $attribute->getName(),
                    'attr'              => array('placeholder' => 'enter value')
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\AttributeValue',
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "attributeValue";
    }
}