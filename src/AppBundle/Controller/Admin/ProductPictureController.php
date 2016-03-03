<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{_locale}/admin", defaults={"_locale": "%locale%"}, requirements={"_locale": "%app.locales%"})
 */
class ProductPictureController extends Controller
{
    /**
     * @Route("/pictures/product/{id}", name="admin_product_pictures",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Template("AppBundle:admin/products:pictures.html.twig")
     */
    public function indexAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:ProductPictures')
            ->findAll();

        return [
            'product'  => $product,
        ];
    }
}