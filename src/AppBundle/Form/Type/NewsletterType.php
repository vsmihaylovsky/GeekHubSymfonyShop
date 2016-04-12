<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/5/16
 * Time: 11:02 PM
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class, ['label' => 'newsletter.subject'])
            ->add('message', TextareaType::class, ['label' => 'newsletter.message', 'attr' => ['class' => 'tinymce']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Newsletter',
        ]);
    }
}