<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/1/16
 * Time: 8:57 PM
 */

namespace AppBundle\Controller\Shop;

use AppBundle\Entity\Invoice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/invoices")
 * @Security("has_role('ROLE_USER')")
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
        $invoices = $em->getRepository('AppBundle:Invoice')->getUserInvoices($user);

        return ['invoices' => $invoices];
    }

    /**
     * @param Invoice $invoice
     * @return array
     * @Route("/{id}", requirements={"id": "\d+"}, name="user_info_invoice")
     * @ParamConverter("invoice", class="AppBundle:Invoice", options={"repository_method" = "getInvoice"})
     * @Method("GET")
     * @Security("is_granted('read', invoice)")
     * @Template("AppBundle:shop/Invoice:user_info_invoice.html.twig")
     */
    public function showInvoiceAction(Invoice $invoice)
    {
        return ['invoice' => $invoice];
    }
}