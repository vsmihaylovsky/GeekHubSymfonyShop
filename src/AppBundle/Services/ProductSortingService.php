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
    const SORT_KEY = 'product_sorting';
    const DEFAULT_SORT_INDEX = 'rating';
    const SORT_TYPES =
        [
            'cheap' => 'product_sort.cheap',
            'expensive' => 'product_sort.expensive',
            'rating' => 'product_sort.rating',
            'novelty' => 'product_sort.novelty',
        ];
    
    private $requestStack;
    private $session;
    private $currentProductSorting;

    public function __construct(RequestStack $requestStack, Session $session)
    {
        $this->requestStack = $requestStack;
        $this->session = $session;

        $request = $this->requestStack->getCurrentRequest();
        if (($request !== null) && $request->query->has(self::SORT_KEY)) {
            $this->session->set(self::SORT_KEY, $request->query->get(self::SORT_KEY));
        }
        $this->currentProductSorting = $this->session->get(self::SORT_KEY, self::DEFAULT_SORT_INDEX);
    }

    public function getCurrentProductSorting() {
        return $this->currentProductSorting;
    }
        
    public function getProductSorting()
    {
        $productSorting = [];
        foreach (self::SORT_TYPES as $sortType => $sortName) {
            $productSorting[] =
                [
                    'sortType' => $sortType,
                    'sortName' => $sortName,
                    'currentSort' => $sortType === $this->currentProductSorting,
                ];
        }

        return $productSorting;
    }    
}