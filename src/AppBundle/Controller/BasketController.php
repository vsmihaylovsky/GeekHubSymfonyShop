<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BasketController extends Controller
{
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
     * @param Product $product
     * @param Request $request
     * @Route("/cart/add/{id}", name="addToCart",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Method({"GET"})
     * @ParamConverter("product", class="AppBundle:Product")
     * @return JsonResponse
     * @throws \Exception
     */
    public function addToCartAction(Product $product, Request $request)
    {
        $response = new JsonResponse();
        $date = new \DateTime('now');
        $date->add(new \DateInterval('P1M'));

        $cart = json_decode($request->cookies->get('cart'), true);
        $productId = $product->getId();
        $cart[$productId] = ($cart ? array_key_exists($productId, $cart) : false)
            ? $cart[$productId] + 1
            : 1;

        //$response->headers->clearCookie('cart');
        $response->headers->setCookie(new Cookie('cart', json_encode($cart, 15), $date));
        $response->setData([
            'adding'    => 'ok',
            'cart'      => $cart ? $cart : 'empty',
        ]);

        return $response;
    }

    /**
     * @param Product $product
     * @param Request $request
     * @Route("/cart/remove/{id}", name="removeFromCart",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Method({"GET"})
     * @ParamConverter("product", class="AppBundle:Product")
     * @return JsonResponse
     * @throws \Exception
     */
    public function removeFromCartAction(Product $product, Request $request)
    {
        $response = new JsonResponse();
        $date = new \DateTime('now');
        $date->add(new \DateInterval('P1M'));

        $cart = json_decode($request->cookies->get('cart'), true);
        $productId = $product->getId();
        if ($cart ? array_key_exists($productId, $cart) : false) unset( $cart[$productId] );

        //$response->headers->clearCookie('cart');
        $response->headers->setCookie(new Cookie('cart', json_encode($cart, 15), $date));
        $response->setData([
            'remove'    => 'ok',
            'id'        => $productId,
            'cart'      => $cart ? $cart : 'empty',
        ]);

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/cart/save", name="saveCart")
     * @Method({"GET"})
     * @return JsonResponse
     */
    public function saveCartAction(Request $request)
    {
        $response = new JsonResponse();
        $jsonCartData = $request->cookies->get('cart');
        $cart = json_decode($jsonCartData, true);
        $response->setData([
            'save' => 'ok',
            'cart' => $cart ? $cart : 'empty',
        ]);

        //Handler will be here.

        return $response;
    }
}
