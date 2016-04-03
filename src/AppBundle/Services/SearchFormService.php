<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 4:31 PM
 */

namespace AppBundle\Services;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeOption;
use AppBundle\Entity\Category;
use AppBundle\Entity\SearchFilter;
use AppBundle\Form\Type\FilterOptionsType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;
use Doctrine\ORM\EntityManager;

class SearchFormService
{
    private $formFactory;
    private $router;
    private $em;

    public function __construct(EntityManager $entityManager,
                                FormFactory $formFactory,
                                Router $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->em = $entityManager;
    }

    public function getSearchForm()
    {
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->formFactory->createBuilder(
            'Symfony\Component\Form\Extension\Core\Type\FormType', null , ['csrf_protection' => false]
        );

        return $formBuilder
            ->setAction($this->router->generate('products_search', [
                'filter' => 'search',
            ]))
            ->setMethod('GET')
            ->add('input', TextType::class, [
                'label'         => false,
                'required'      => true,
                'attr'          => ['placeholder' => 'search']
            ])
            ->add('search', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();
    }

    public function getFilterForm($categorySlug)
    {
        $currentCategory = $this->em->getRepository('AppBundle:Category')->findOneBy(['slug' => $categorySlug]);
        if($currentCategory instanceof Category) {
            $search = new SearchFilter();
            $search->setCategory($currentCategory);
            if($currentCategory->getAttributes()->count() > 0) {
                foreach ($currentCategory->getAttributes() as $filter) {
                    /** @var Attribute $filter */
                    if($filter->getType() == 'select' && $filter->getFilterable() == 1) {
                        $search->addFilter($filter);
                    }
                }
                /** @var FormBuilder $formBuilder */
                $formBuilder = $this->formFactory->createBuilder(
                    'Symfony\Component\Form\Extension\Core\Type\FormType',
                    $search, [
                        'csrf_protection' => false,
                    ]
                );

                return $formBuilder
                    ->setAction($this->router->generate('products_filtered', [
                        'filter' => 'category',
                        'param'  => $categorySlug
                    ]))
                    ->setMethod('GET')
                    ->add('filters', CollectionType::class, array(
                        'entry_type'    => FilterOptionsType::class,
                        'label'         => false,
                        'required'      => false
                    ))
                    ->add('filter', SubmitType::class, [
                        'attr' => ['class' => 'btn btn-primary']
                    ])
                    ->getForm();
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function prepareFiltersData($data)
    {
        $preparedParams = [];

        if($data && $data instanceof SearchFilter) {
            $preparedParams['category'] = $data->getCategory()->getId();
            $filters = $data->getFilters();
            if($filters && $filters->count() > 0) {
                foreach($filters as $filter) {
                    /** @var Attribute $filter */
                    $params = $filter->getParams();
                    if($params && count($params) > 0) {
                        foreach($params as $param) {
                            /** @var AttributeOption $param */
                            $preparedParams['options'][] = $param->getId();
                        }
                    }
                }
            }
        }

        return $preparedParams;
    }
}