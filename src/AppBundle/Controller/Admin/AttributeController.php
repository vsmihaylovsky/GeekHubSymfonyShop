<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeOption;
use AppBundle\Form\Type\AttributeType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 */
class AttributeController extends Controller
{
    /**
     * @Route("/attributes", name="admin_attributes")
     * @Template("AppBundle:admin/attributes:attributes.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $attributes = $em->getRepository('AppBundle:Attribute')
            ->findAll();

        return [
            'attributes'  => $attributes,
        ];
    }

    /**
     * @param $id
     * @param $action
     * @param Request $request
     * @Route("/attribute/{action}/{id}", name="admin_attribute_edit",
     *     defaults={"id": 0, "action": "new"},
     *     requirements={
     *      "action": "new|edit",
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/attributes/form:attribute.html.twig")
     *
     * @return Response
     */
    public function attributeEditAction($id, $action, Request $request)
    {
//        TODO: Add check on unique options for attribute

        $em = $this->getDoctrine()->getManager();
        $originalOptions = new ArrayCollection();

        if ($action == "edit") {
            $attribute = $em->getRepository('AppBundle:Attribute')
                ->find($id);
            $title = 'Edit attribute id: '.$id;


            foreach ($attribute->getOptions() as $option) {
                $originalOptions->add($option);
            }
        }
        else {
            $attribute = new Attribute();
            $attribute->addOption(new AttributeOption());
            $title = 'Create new attribute';
        }

        $form = $this->createForm(AttributeType::class, $attribute, [
            'em' => $em,
            'action' => $this->generateUrl('admin_attribute_edit', ['action' => $action, 'id' => $id]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                foreach ($originalOptions as $option) {
                    if (false === $attribute->getOptions()->contains($option)) {
                         $em->remove($option);
                    }
                }
                $em->persist($attribute);
                $em->flush();

                if ($attribute->getId()) {
                    return $this->redirectToRoute('admin_attribute_edit', ['action' => 'edit', 'id' => $attribute->getId()]);
                } else {
                    return $this->redirectToRoute('admin_attribute_edit');
                }
            }
        }

        $formData = $form->createView();

        return [
            'title' => $title,
            'form'  => $formData,
        ];
    }



}
