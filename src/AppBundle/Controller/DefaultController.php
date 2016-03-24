<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("AppBundle:shop:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $latest = $em->getRepository('AppBundle:Product')->getLatestProductsWithPictures();

        return [
            'latestProducts'  => $latest,
        ];
    }

    /**
     * @param $page
     * @param Request $request
     * @return Response
     * @Route("/products/{pager}/{page}", name="products",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*"
     *     })
     * @Template("AppBundle:shop:products.html.twig")
     */
    public function productsAction($page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Product')->getProductsWithPictures();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $limit = 9);

        return [
            'products'  => $pagination,
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
     *          "filter": "none|category",
     *          "pager": "page",
     *          "page": "[1-9]\d*"
     *     })
     * @Template("AppBundle:shop:products.html.twig")
     */
    public function productsFilteredAction($filter, $param, $page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Product')->getFilteredProductsWithPictures($filter, $param);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $limit = 9);

        return [
            'products'  => $pagination,
        ];
    }

    /**
     * @param $slug
     * @param Request $request
     * @Route("/product/{slug}", name="product_view")
     * @Template("AppBundle:shop:details.html.twig")
     * @return array
     */
    public function detailsAction($slug, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')
            ->getProductWithDep($slug);

        return [
            'product'  => $product,
        ];
    }

    /**
     * @Route("/login1", name="login1")
     * @Template("AppBundle:shop:login.html.twig")
     */
    public function loginAction(Request $request)
    {
        return [];
    }
}
