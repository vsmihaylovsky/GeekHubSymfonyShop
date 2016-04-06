<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/3/16
 * Time: 2:46 PM
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\InvoiceStatus;
use AppBundle\Form\Type\InvoiceStatusType;
use AppBundle\Form\Type\InvoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Invoice;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/invoice")
 */
class InvoiceController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @Route("/", name="admin_invoices")
     * @Method("GET")
     * @Template("AppBundle:admin/invoice:list.html.twig")
     */
    public function showAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Invoice')->getAllQuery($request->query->get('search'));

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1),/*page number*/
            $request->query->getInt('row-per-page', 10),
            ['defaultSortFieldName' => 'i.createdAt', 'defaultSortDirection' => 'desc']
        );

        return ['pagination' => $pagination];
    }

    /**
     * @param Request $request
     * @param Invoice $invoice
     * @return array
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="show_invoice")
     * @ParamConverter("invoice", class="AppBundle:Invoice", options={"repository_method" = "getInvoice"})
     * @Method({"GET", "POST", "PUT"})
     * @Template("AppBundle:admin/invoice:show.html.twig")
     */
    public function showAction(Request $request, Invoice $invoice)
    {
        $invoiceForm = $this->createForm(InvoiceType::class, $invoice, [
            'action' => $this->generateUrl('show_invoice', ['id' => $invoice->getId()]),
            'method' => Request::METHOD_PUT,
            'attr'   => ['name' => 'checkout'],
        ])
            ->add('save', SubmitType::class, ['label' => 'Save']);

        $invoiceForm->handleRequest($request);
        if ($invoiceForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('show_invoice', ['id' => $invoice->getId()]));
        }

        $invoiceStatus = new InvoiceStatus;
        $invoiceStatusForm = $this->createForm(InvoiceStatusType::class, $invoiceStatus, [
            'action' => $this->generateUrl('show_invoice', ['id' => $invoice->getId()]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, ['label' => 'invoice.add_status']);

        $invoiceStatusForm->handleRequest($request);
        if ($invoiceStatusForm->isValid()) {
            $invoiceStatus->setInvoice($invoice);
            $user = $this->getUser();
            $invoiceStatus->setManager($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($invoiceStatus);
            $em->flush();

            return $this->redirect($this->generateUrl('show_invoice', ['id' => $invoice->getId()]));
        }

        return [
            'invoice' => $invoice,
            'invoiceForm' => $invoiceForm->createView(),
            'invoiceStatusForm' => $invoiceStatusForm->createView(),
        ];
    }
}