<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 4:31 PM
 */

namespace AppBundle\Services;

use AppBundle\Entity\Review;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

class DeleteFormService
{
    private $formFactory;
    private $router;

    public function __construct(FormFactory $formFactory, Router $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form The form
     */
    public function createReviewDeleteForm($id)
    {
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->formFactory->createBuilder();

        return $formBuilder
            ->setAction($this->router->generate('delete_review', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * @param $reviews
     * @return array
     */
    public function getReviewsDeleteForms($reviews) {
        $delete_forms = [];
        /** @var Review $review */
        foreach ($reviews as $review) {
            $delete_forms[$review->getId()] = $this->createReviewDeleteForm($review->getId())->createView();
        }

        return $delete_forms;
    }
}