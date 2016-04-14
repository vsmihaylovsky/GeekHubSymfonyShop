<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/30/16
 * Time: 9:42 PM
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Type\PrivateMessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\PrivateMessage;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/private-message")
 */
class PrivateMessageController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @Route("/", name="admin_private_messages")
     * @Method("GET")
     * @Template("AppBundle:admin/private_message:list.html.twig")
     */
    public function showAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:PrivateMessage')->getAllQuery($request->query->get('search'));

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1),/*page number*/
            $request->query->getInt('row-per-page', 10),
            ['defaultSortFieldName' => 'p.sentTime', 'defaultSortDirection' => 'desc']
        );

        $delete_forms = $this->get('app.delete_form_service')->getEntitiesDeleteForms($pagination, 'delete_private_message');

        return
            [
                'pagination' => $pagination,
                'delete_forms' => $delete_forms
            ];
    }

    /**
     * @param PrivateMessage $privateMessage
     * @return array
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="edit_private_message")
     * @Method("GET")
     * @Template("AppBundle:admin/private_message:form.html.twig")
     */
    public function editAction(PrivateMessage $privateMessage)
    {
        $form = $this->createForm(PrivateMessageType::class, $privateMessage, [
            'action' => $this->generateUrl('update_private_message', ['id' => $privateMessage->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'table.update']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param PrivateMessage $privateMessage
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | array
     * @Route("/{id}", requirements={"id": "\d+"}, name="update_private_message")
     * @Method("PUT")
     * @Template("AppBundle:admin/private_message:form.html.twig")
     */
    public function updateAction(Request $request, PrivateMessage $privateMessage)
    {
        $form = $this->createForm(PrivateMessageType::class, $privateMessage, [
            'action' => $this->generateUrl('update_private_message', ['id' => $privateMessage->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_private_messages'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param PrivateMessage $privateMessage
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", requirements={"id": "\d+"}, name="delete_private_message")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PrivateMessage $privateMessage)
    {
        $form = $this->get('app.delete_form_service')->createEntityDeleteForm($privateMessage->getId(), 'delete_private_message');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($privateMessage);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_private_messages'));
    }
}