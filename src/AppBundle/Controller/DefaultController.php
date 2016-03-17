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
        return [];
    }

    /**
     * @param $page
     * @param Request $request
     * @return Response
     * @Route("/items/{pager}/{page}", name="items",
     *     defaults={"pager": "page", "page": 1},
     *     requirements={
     *          "pager": "page",
     *          "page": "[1-9]\d*"
     *     })
     * @Template("AppBundle:shop:items.html.twig")
     */
    public function itemsAction($page, Request $request)
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
     * @param $id
     * @param Request $request
     * @Route("/product/{id}", name="product_view",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Template("AppBundle:shop:details.html.twig")
     * @return array
     */
    public function detailsAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')
            ->getProductWithDep($id);

        return [
            'product'  => $product,
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
