<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 4:31 PM
 */

namespace AppBundle\Services;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
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
    public function createEntityDeleteForm($id, $deleteEntityRoute)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $this->formFactory->createBuilder();

        return $formBuilder
            ->setAction($this->router->generate($deleteEntityRoute, ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * @param $entities
     * @return array
     */
    public function getEntitiesDeleteForms($entities, $deleteEntityRoute) {
        $delete_forms = [];
        foreach ($entities as $entity) {
            $delete_forms[$entity->getId()] = $this->createEntityDeleteForm($entity->getId(), $deleteEntityRoute)->createView();
        }

        return $delete_forms;
    }
}