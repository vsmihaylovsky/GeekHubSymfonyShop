<?php

namespace AppBundle\Services;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeOption;
use AppBundle\Entity\Category;
use AppBundle\Entity\SearchFilter;
use AppBundle\Form\Type\FilterOptionsType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'attr'          => ['placeholder' => 'form.search_term']
            ])
            ->add('search', SubmitType::class, [
                'label'         => 'form.search',
                'attr'          => ['class' => 'btn btn-primary']
            ])
            ->getForm();
    }

    public function getFilterForm($categorySlug)
    {
        $currentCategory = $this->em->getRepository('AppBundle:Category')->findOneBy(['slug' => $categorySlug]);
        $search = new SearchFilter();
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->formFactory->createBuilder(
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            $search, [
                'csrf_protection' => false,
            ]
        );
        $dir = $search->getDirections();
        $formBuilder
            ->setMethod('GET')
            ->add('sort', ChoiceType::class, [
                'choices'  => [
                    'date'  => 'date',
                    'price' => 'price',
                    'name'  => 'name',
                ],
                'label'             => 'Sort',
                'choices_as_values' => true,
                'mapped'            => false,
            ])
            ->add('directions', ChoiceType::class, [
                'choices'           => $search->getDirections(),
                'label'             => 'Directions',
                'choices_as_values' => true,
                //'mapped'            => false,
            ])
            ->add('sale', ChoiceType::class, [
                'choices'  => [
                    'sale' => 'sale',
                ],
                'expanded'          => true,
                'multiple'          => true,
                'label'             => 'Sale',
                'choices_as_values' => true,
                'mapped'            => false,
            ])
            ->add('filter', SubmitType::class, [
                'label'         => 'form.filter',
                'attr'          => ['class' => 'btn btn-primary']
            ]);
        if($currentCategory instanceof Category) {
            $search->setCategory($currentCategory);
            if($currentCategory->getAttributes()->count() > 0) {
                foreach ($currentCategory->getAttributes() as $filter) {
                    /** @var Attribute $filter */
                    if($filter->getType() == 'select' && $filter->getFilterable() == 1) {
                        $search->addFilter($filter);
                    }
                }

                $formBuilder
                    ->setAction($this->router->generate('products_filtered', [
                        'filter' => 'category',
                        'param'  => $categorySlug
                    ]))
                    ->add('filters', CollectionType::class, [
                        'entry_type'    => FilterOptionsType::class,
                        'label'         => false,
                        'required'      => false
                    ]);
                $data['category'] = $currentCategory->getTitle();
            }
        } else {
            $formBuilder
                ->setAction($this->router->generate('products_filtered', [
                    'filter' => 'filter',
                ]));
            $data['category'] = "Filtered";
        }

        $data['form'] = $formBuilder->getForm();

        return $data;
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
                            $preparedParams['options'][$filter->getId()][$param->getId()] = $param->getId();
                        }
                    }
                }
            }
        }

        return $preparedParams;
    }
}