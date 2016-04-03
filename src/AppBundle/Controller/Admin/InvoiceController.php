<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/3/16
 * Time: 2:46 PM
 */

namespace AppBundle\Controller\Admin;

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
     * @param Invoice $invoice
     * @return array
     * @Route("/edit/{id}", name="show_invoice")
     * @ParamConverter("invoice", class="AppBundle:Invoice")
     * @Method("GET")
     * @Template("AppBundle:admin/invoice:form.html.twig")
     */
    public function editAction(Invoice $invoice)
    {
        $form = $this->createForm(InvoiceType::class, $invoice, [
            'action' => $this->generateUrl('update_invoice', ['id' => $invoice->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'table.update']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param Invoice $invoice
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", name="update_invoice")
     * @ParamConverter("invoice", class="AppBundle:Invoice")
     * @Method("PUT")
     * @Template("AppBundle:admin/invoice:form.html.twig")
     */
    public function updateAction(Request $request, Invoice $invoice)
    {
        $form = $this->createForm(InvoiceType::class, $invoice, [
            'action' => $this->generateUrl('update_invoice', ['id' => $invoice->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_invoices'));
        }

        return ['form' => $form->createView()];
    }
}