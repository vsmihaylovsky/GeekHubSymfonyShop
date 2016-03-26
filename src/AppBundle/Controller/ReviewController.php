<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/25/16
 * Time: 10:34 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Review;
use AppBundle\Form\Type\ReviewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{slug}/{page}", defaults={"page" = 1}, name="show_product_reviews")
     * @Method("GET")
     * @Template("AppBundle:shop/Review:product_reviews.html.twig")
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{slug}", name="create_review")
     * @ParamConverter("product", class="AppBundle:Product")
     * @Method("POST")
     * @Template("AppBundle:shop/Review:form.html.twig")
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