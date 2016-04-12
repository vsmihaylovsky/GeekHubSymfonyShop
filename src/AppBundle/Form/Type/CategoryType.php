<?php
namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    protected $em;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->em = $options['em'];

        $builder
            ->add('title', TextType::class, array(
                    'attr' => array('placeholder' => 'Enter category title')
                )
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            if ($event->getData()->getChildren()->isEmpty()) {
                $choices = $this->getChoices();
                $event->getForm()
                    ->add('parent', EntityType::class, array(
                        'class'         => 'AppBundle:Category',
                        'choices'       => $choices,
                        'choice_label'  => 'title',
                        'placeholder'   => 'without parent category',
                        'empty_data'    => null,
                        'required'      => false,
                    ))
                    ->add('attributes', EntityType::class, array(
                        'class'         => 'AppBundle:Attribute',
                        'choice_label'  => 'name',
                        'placeholder'   => 'Choose attributes',
                        'expanded'      => 'true',
                        'multiple'      => 'true',
                        'required'      => false,
                    ));
            }
        });
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

    protected function getChoices()
    {
        /** @var EntityManager $em */
        $data = $this->em->getRepository('AppBundle:Category')->getCategoryWithCounts();
        $choices = [];
        foreach ($data as $item) {
            if ($item['parentId'] == null && $item['countProducts'] == 0) {
                $choices[] = $item[0];
            }
        }
        return $choices;
    }
}