<?php

namespace AppBundle\Services;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceItem;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CartHandler
{
    private $em;
    private $formFactory;
    private $router;
    private $tokenStorage;

    /**
     * PrivateMessagesService constructor.
     * @param EntityManager $entityManager
     * @param FormFactory $formFactory
     * @param Router $router
     * @param TokenStorage $tokenStorage
     */
    public function __construct(EntityManager $entityManager,
                                FormFactory $formFactory,
                                Router $router,
                                TokenStorage $tokenStorage)
    {
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function createInvoiceEntity(Request $request)
    {
        $json = $request->cookies->get('cart');
        $cart = json_decode($json, true);
        $invoice = new Invoice();
        if($cart && is_array($cart)) {
            $amount = 0;
            foreach($cart as $id => $qty) {
                $product = $this->em->getRepository('AppBundle\Entity\Product')->find($id);
                if($product instanceof Product && $qty > 0) {
                    $price = $product->getPrice();
                    $item = new InvoiceItem();
                    $item
                        ->setProduct($product)
                        ->setPrice($price)
                        ->setQty((int)$qty);
                    $invoice->addItem($item);
                    $amount += $price * $qty;
                }
            }
            $invoice->setAmount($amount);
            $user = $this->tokenStorage->getToken()->getUser();
            if($user != 'anon.') {
                /** @var User $user */
                $invoice
                    ->setCustomer($user)
                    ->setCustomerName($user->getUsername());
            }
        }

        return $invoice;
    }

    /**
     * @param $cart
     * @return Cookie
     */
    public function prepareCookie($cart)
    {
        $date = new \DateTime('now');
        $date->add(new \DateInterval('P1M'));

        return new Cookie('cart', json_encode($cart, 15), $date);
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return array $cart
     */
    public function addToCart(Product $product, Request $request)
    {
        $cart = json_decode($request->cookies->get('cart'), true);
        $productId = $product->getId();
        $cart[$productId] = (is_array($cart) ? array_key_exists($productId, $cart) : false)
            ? $cart[$productId] + 1
            : 1;

        return $cart;
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return array $cart
     */
    public function removeFromCart(Product $product, Request $request)
    {
        $cart = json_decode($request->cookies->get('cart'), true);
        $productId = $product->getId();
        if (is_array($cart) ? array_key_exists($productId, $cart) : false) unset( $cart[$productId] );

        return $cart;
    }

    public function updateCart(Invoice $invoice)
    {
        $cart = [];
        $items = $invoice->getItems();
        if($items->count() > 0) {
            foreach($items as $item) {
                /** @var InvoiceItem $item */
                $productId = $item->getProduct()->getId();
                $qty = ($item->getQty() > 0) ? $item->getQty() : 1;
                $cart[$productId] = $qty;
            }
        }

        return $cart;
    }
}