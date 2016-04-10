<?php

namespace AppBundle\Controller\Shop;

use AppBundle\Entity\Product;
use AppBundle\Form\Type\InvoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CartController extends Controller
{
    /**
     * @Route("/checkout", name="checkout")
     * @Template("AppBundle:shop:checkout.html.twig")
     */
    public function checkoutAction(Request $request)
    {
        $invoice = $this->get('app.cart_handler')->createInvoiceEntity($request);

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(InvoiceType::class, $invoice, [
            'em' => $em,
            'action' => $this->generateUrl('checkout'),
            'method' => Request::METHOD_POST,
            'attr'   => ['name' => 'checkout'],
        ])
            ->add('order', SubmitType::class, [
                'label' => 'Order',
                'attr'  => ['class' => 'btn btn-primary']
            ]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($invoice);
                $em->flush();
                $response = new RedirectResponse($this->generateUrl('homepage'));
                $response->headers->clearCookie('cart');

                return $response;
            }
        }

        return [
            'invoice'   => $invoice,
            'form'      => $form->createView(),
        ];
    }

    /**
     * @Route("/cart", name="cart")
     * @Template("AppBundle:shop:cart.html.twig")
     */
    public function cartAction(Request $request)
    {
        $invoice = $this->get('app.cart_handler')->createInvoiceEntity($request);

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(InvoiceType::class, $invoice, [
                'em' => $em,
                'action' => $this->generateUrl('cart'),
                'method' => Request::METHOD_POST,
                'attr'   => ['name' => 'cart'],
            ])
            ->add('update', SubmitType::class, [
                'label' => 'Update',
                'attr'  => ['class' => 'btn btn-primary']
            ]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $cart = $this->get('app.cart_handler')->updateCart($invoice);
                $response = new RedirectResponse($this->generateUrl('cart'));
                $cookie = $this->get('app.cart_handler')->prepareCookie($cart);
                $response->headers->setCookie($cookie);

                return $response;
            }
        }

        return [
            'invoice'   => $invoice,
            'form'      => $form->createView(),
        ];
    }

    /**
     * @param Product $product
     * @param Request $request
     * @Route("/cart/add/{id}", name="addToCart",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @ParamConverter("product", class="AppBundle:Product")
     * @return JsonResponse
     * @throws \Exception
     */
    public function addToCartAction(Product $product, Request $request)
    {
        $cart = $this->get('app.cart_handler')->addToCart($product, $request);
        $response = new JsonResponse();
        $cookie = $this->get('app.cart_handler')->prepareCookie($cart);
        $response->headers->setCookie($cookie);
        $response->setData([
            'product' => $product->getName()
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
        $cart = $this->get('app.cart_handler')->removeFromCart($product, $request);
        $response = new RedirectResponse($this->generateUrl('cart'));
        $cookie = $this->get('app.cart_handler')->prepareCookie($cart);
        $response->headers->setCookie($cookie);

        return $response;
    }

}
