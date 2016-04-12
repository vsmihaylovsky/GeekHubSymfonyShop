<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/3/16
 * Time: 6:23 PM
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceStatusType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class,
                [
                    'choices'  =>
                        [
                            'invoice.status.is_gathered',
                            'invoice.status.is_sent',
                            'invoice.status.is_done',
                            'invoice.status.is_canceled',
                        ],
                    'expanded' => false,
                    'multiple' => false,
                    'label' => 'invoice.status_label',
                    'choices_as_values' => true,
                    'choice_label' => function ($value, $key, $index) {
                            return $value;
                    },
                    'placeholder' => '',
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\InvoiceStatus',
        ]);
    }

    public function getBlockPrefix()
    {
        return "invoiceStatus";
    }
}