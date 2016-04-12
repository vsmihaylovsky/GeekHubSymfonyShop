<?php

namespace AppBundle\Controller\Shop;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Template("AppBundle:shop:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $latest = $em->getRepository('AppBundle:Product')->getLatestProductsWithPictures();

        return [
            'latestProducts'  => $latest,
        ];
    }

    /**
     * @param $filter
     * @param $param
     * @param $page
     * @param Request $request
     * @return Response
     * @Route("/products/{filter}/{param}/{pager}/{page}", name="products_filtered",
     *     defaults={"filter": "none", "param": "none", "pager": "page", "page": 1},
     *     requirements={
     *          "filter": "none|category|filter",
     *          "pager": "page",
     *          "page": "[1-9]\d*"
     *     })
     * @Method("GET")
     * @Template("AppBundle:shop:products.html.twig")
     */
    public function productsFilteredAction($filter, $param, $page, Request $request)
    {
        $params = $filter == 'category' ? ['category' => $param] : '';

        $searchService = $this->get('app.search_form_service');
        $filterFormData = $searchService->getFilterForm($param);
        $filterForm = $filterFormData['form'];
        /** @var Form $filterForm */
        $filterForm->handleRequest($request);
        if ($filterForm->isValid()) {
            $filter = 'filter';
            $params = $searchService->prepareFiltersData($filterForm->getData());
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Product')->getFilteredProductsWithPictures($filter, $params, $this->get('app.product_sorting_service')->getCurrentProductSorting());
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $limit = 9);

        return [
            'title'     => $filterFormData['category'],
            'products'  => $pagination,
        ];
    }

    /**
     * @param $page
     * @param Request $request
     * @return Response
     * @Route("/search/{pager}/{page}", name="products_search",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*"
     *     })
     * @Method("GET")
     * @Template("AppBundle:shop:products.html.twig")
     */
    public function searchAction($page, Request $request)
    {
        $searchService = $this->get('app.search_form_service');
        $filterForm = $searchService->getSearchForm();
        /** @var Form $filterForm */
        $filterForm->handleRequest($request);
        if ($filterForm->isValid()) {
            $params = [];
            $params['name'] = $filterForm->get('input') ? $filterForm->get('input')->getData() : '';
            $em = $this->getDoctrine()->getManager();
            $query = $em->getRepository('AppBundle:Product')->getFilteredProductsWithPictures('search', $params, $this->get('app.product_sorting_service')->getCurrentProductSorting());
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate($query, $page, $limit = 9);

            return [
                'title'     => 'Search result',
                'products'  => $pagination,
            ];
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param Product $product
     * @param $tab
     * @return array
     * @Route("/product/{slug}/{tab}", defaults={"tab" = "details"}, name="product_view")
     * @Method("GET")
     * @ParamConverter("product", class="AppBundle:Product", options={
     *     "repository_method" = "getProductWithJoins",
     *     "map_method_signature" = true
     * })
     * @Template("AppBundle:shop:details.html.twig")
     */
    public function detailsAction(Product $product, $tab)
    {
        return [
            'product'   => $product,
            'tab'       => $tab,
        ];
    }

    /**
     * @Route("/login1", name="login1")
     * @Template("AppBundle:shop:login.html.twig")
     */
    public function loginAction()
    {
        return [];
    }
}
