<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 2/7/16
 * Time: 11:01 PM
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Type\ReviewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Review;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/review")
 */
class ReviewController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @Route("/", name="admin_reviews")
     * @Method("GET")
     * @Template("AppBundle:admin/review:list.html.twig")
     */
    public function showAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Review')->getAllQuery($request->query->get('search'));

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1),/*page number*/
            $request->query->getInt('row-per-page', 10),
            ['defaultSortFieldName' => 'r.createdAt', 'defaultSortDirection' => 'desc']
        );

        $delete_forms = $this->get('app.delete_form_service')->getEntitiesDeleteForms($pagination, 'delete_review');

        return
            [
                'pagination' => $pagination,
                'delete_forms' => $delete_forms
            ];
    }

    /**
     * @param Review $review
     * @return array
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="edit_review")
     * @Method("GET")
     * @Template("AppBundle:admin/review:form.html.twig")
     */
    public function editAction(Review $review)
    {
        $form = $this->createForm(ReviewType::class, $review, [
            'action' => $this->generateUrl('update_review', ['id' => $review->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'table.update']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param Review $review
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", requirements={"id": "\d+"}, name="update_review")
     * @Method("PUT")
     * @Template("AppBundle:admin/review:form.html.twig")
     */
    public function updateAction(Request $request, Review $review)
    {
        $form = $this->createForm(ReviewType::class, $review, [
            'action' => $this->generateUrl('update_review', ['id' => $review->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_reviews'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param Review $review
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", requirements={"id": "\d+"}, name="delete_review")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Review $review)
    {
        $form = $this->get('app.delete_form_service')->createEntityDeleteForm($review->getId(), 'delete_review');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($review);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_reviews'));
    }
}