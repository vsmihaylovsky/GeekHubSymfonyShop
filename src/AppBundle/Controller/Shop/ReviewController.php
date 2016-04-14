<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/25/16
 * Time: 10:34 PM
 */

namespace AppBundle\Controller\Shop;

use AppBundle\Entity\Product;
use AppBundle\Entity\Review;
use AppBundle\Form\Type\ReviewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/review")
 */

class ReviewController extends Controller
{
    /**
     * @param $slug
     * @param $page
     * @Route("/{slug}/{page}", defaults={"page" = 1}, name="show_product_reviews")
     * @Method("GET")
     * @Template("AppBundle:shop/Review:product_reviews.html.twig")
     * @return array
     */
    public function showProductReviewsAction($slug, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Review')->getProductReviewsQuery($slug);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page,/*page number*/
            5
        );

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review, [
            'action' => $this->generateUrl('create_review', ['slug' => $slug]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'review.send']);

        return
            [
                'pagination' => $pagination,
                'form' => $form->createView(),
            ];
    }

    /**
     * @param Request $request
     * @param Product $product
     * @Route("/{slug}", name="create_review")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @Template("AppBundle:shop/Review:form.html.twig")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request, Product $product)
    {
        $user = $this->getUser();

        $Review = new Review();
        $Review->setProduct($product);
        $Review->setUser($user);

        $form = $this->createForm(ReviewType::class, $Review, [
            'action' => $this->generateUrl('create_review', ['slug' => $product->getSlug()]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'review.send']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($Review);
            $em->flush();

            return $this->redirect($this->generateUrl('product_view', ['slug' => $product->getSlug(), 'tab' => 'reviews']));
        }

        return
            [
                'form' => $form->createView(),
            ];
    }
}