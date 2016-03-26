<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Review;
use AppBundle\Form\Type\ReviewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
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
     * @param $page
     * @param Request $request
     * @return Response
     * @Route("/products/{pager}/{page}", name="products",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*"
     *     })
     * @Method("GET")
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
     * @Method("GET")
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
            'product'  => $product,
            'tab'  => $tab,
        ];
    }

    /**
     * @Route("/checkout", name="checkout")
     * @Template("AppBundle:shop:checkout.html.twig")
     */
    public function checkoutAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/cart", name="cart")
     * @Template("AppBundle:shop:cart.html.twig")
     */
    public function cartAction(Request $request)
    {
        return [];
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
