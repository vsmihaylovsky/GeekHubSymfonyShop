<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/12/16
 * Time: 12:35 AM
 */

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductSortingService
{
    const SORT_PARAMETER_NAME = 'product_sorting';
    const DEFAULT_SORT_INDEX = 2;
    const SORT_TYPES =
        [
            [
                'parameter' => 'cheap',
                'caption' => 'product_sort.cheap',
                'orderField' => 'p.price',
                'orderDirection' => 'ASC',
            ],
            [
                'parameter' => 'expensive',
                'caption' => 'product_sort.expensive',
                'orderField' => 'p.price',
                'orderDirection' => 'DESC',
            ],
            [
                'parameter' => 'rating',
                'caption' => 'product_sort.rating',
                'orderField' => 'p.rating',
                'orderDirection' => 'DESC',
            ],
            [
                'parameter' => 'novelty',
                'caption' => 'product_sort.novelty',
                'orderField' => 'p.createdAt',
                'orderDirection' => 'DESC',
            ],
        ];
    
    private $requestStack;
    private $session;
    private $currentProductSorting;

    public function __construct(RequestStack $requestStack, Session $session)
    {
        $this->requestStack = $requestStack;
        $this->session = $session;

        $request = $this->requestStack->getCurrentRequest();
        if (($request !== null) && $request->query->has(self::SORT_PARAMETER_NAME)) {
            $this->session->set(self::SORT_PARAMETER_NAME, $request->query->get(self::SORT_PARAMETER_NAME));
        }

        $this->setCurrentProductSorting();
    }

    private function setCurrentProductSorting()
    {
        $this->currentProductSorting = self::SORT_TYPES[self::DEFAULT_SORT_INDEX];

        $currentProductSortingParameter = $this->session->get(self::SORT_PARAMETER_NAME, self::SORT_TYPES[self::DEFAULT_SORT_INDEX]['parameter']);
        foreach (self::SORT_TYPES as $sortType) {
            if ($sortType['parameter'] === $currentProductSortingParameter) {
                $this->currentProductSorting = $sortType;
            }
        }
    }

    public function getCurrentProductSorting()
    {
        return $this->currentProductSorting;
    }

    public function getProductSorting()
    {
        $productSorting = [];
        foreach (self::SORT_TYPES as $sortType) {
            $productSorting[] =
                [
                    'parameter' => $sortType['parameter'],
                    'caption' => $sortType['caption'],
                    'currentSort' => $sortType === $this->currentProductSorting,
                ];
        }

        return $productSorting;
    }    
}