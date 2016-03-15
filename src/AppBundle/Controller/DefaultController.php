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
     * @Route("/items", name="items")
     * @Template("AppBundle:shop:items.html.twig")
     */
    public function itemsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->getProductsWithPictures();

        return [
            'products'  => $products,
        ];
    }

    /**
     * @Route("/details", name="details")
     * @Template("AppBundle:shop:details.html.twig")
     */
    public function detailsAction(Request $request)
    {
        return [];
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
