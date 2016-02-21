<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Attribute;
use AppBundle\Form\Type\AttributeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{_locale}/admin", defaults={"_locale": "%locale%"}, requirements={"_locale": "%app.locales%"})
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
        $em = $this->getDoctrine()->getManager();
        if ($action == "edit") {
            $role = $em->getRepository('AppBundle:Attribute')
                ->find($id);
            $title = 'Edit attribute id: '.$id;
        }
        else {
            $role = new Attribute();
            $title = 'Create new attribute';
        }

        $form = $this->createForm(AttributeType::class, $role, [
            'em' => $em,
            'action' => $this->generateUrl('admin_attribute_edit', ['action' => $action, 'id' => $id]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($role);
                $em->flush();

                return $this->redirectToRoute('admin_attribute_edit');
            }
        }

        $formData = $form->createView();

        return [
            'title' => $title,
            'form'  => $formData,
        ];
    }



}
