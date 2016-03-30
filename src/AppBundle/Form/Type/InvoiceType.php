<?php
namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $formOptions = $event->getForm()->getConfig()->getOptions();
            $formName = $formOptions['attr']['name'];
            if ($formName == 'checkout') {
                $event->getForm()
                    ->add('customerName', TextType::class, [
                            'label' => false,
                            'attr'  => [
                                'placeholder' => 'Name*'
                            ]
                        ]
                    )->add('email', TextType::class, [
                            'label' => false,
                            'attr'  => [
                                'placeholder' => 'Email*'
                            ]
                        ]
                    )->add('phone', TextType::class, [
                            'label' => false,
                            'attr'  => [
                                'placeholder' => 'Phone*'
                            ]
                        ]
                    )->add('delivery', TextType::class, [
                            'label' => false,
                            'attr'  => [
                                'placeholder' => 'Address*'
                            ]
                        ]
                    )->add('payment', TextType::class, [
                            'label' => false,
                            'attr'  => [
                                'placeholder' => 'Payment*'
                            ]
                        ]
                    )->add('comment', TextareaType::class, [
                            'label' => false,
                            'attr'  => [
                                'placeholder' => 'Notes about your order, Special Notes for Delivery',
                                'rows'        => '16'
                            ],
                            'required' => false
                        ]
                    );
            } elseif ($formName == 'cart') {
                $event->getForm()->add('items', CollectionType::class, array(
                    'entry_type'    => InvoiceItemType::class,
                    'allow_delete'  => true,
                    'by_reference'  => false,
                    'label'         => 'Item',
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Invoice',
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return "invoice";
    }
}