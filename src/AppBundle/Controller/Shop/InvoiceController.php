<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/1/16
 * Time: 8:57 PM
 */

namespace AppBundle\Controller\Shop;

use AppBundle\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/invoices")
 */
class InvoiceController extends Controller
{
    /**
     * @return array
     * @Route("/", name="user_info_invoices")
     * @Method("GET")
     * @Template("AppBundle:shop/Invoice:user_info_list.html.twig")
     */
    public function showAllAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $invoices = $em->getRepository('AppBundle:Invoice')->findBy(['customer' => $user], ['createdAt' => 'desc']);

        return ['invoices' => $invoices];
    }
}