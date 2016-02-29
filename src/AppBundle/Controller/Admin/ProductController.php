<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeValue;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Form\Type\ProductType;
use AppBundle\Form\Type\ProductAttributesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{_locale}/admin", defaults={"_locale": "%locale%"}, requirements={"_locale": "%app.locales%"})
 */
class ProductController extends Controller
{
    /**
     * @Route("/products", name="admin_products")
     * @Template("AppBundle:admin/products:products.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->findAll();

        return [
            'products'  => $products,
        ];
    }

    /**
     * @param $id
     * @param $action
     * @param Request $request
     * @Route("/product/{action}/{id}", name="admin_product_edit",
     *     defaults={"id": 0, "action": "new"},
     *     requirements={
     *      "action": "new|edit",
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/products/form:product.html.twig")
     *
     * @return Response
     */
    public function productEditAction($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($action == "edit") {
            $product = $em->getRepository('AppBundle:Product')
                ->find($id);
            $title = 'Edit product id: '.$id;
        }
        else {
            $product = new Product();
            $title = 'Create new product';
        }

        $form = $this->createForm(ProductType::class, $product, [
            'em' => $em,
            'action' => $this->generateUrl('admin_product_edit', ['action' => $action, 'id' => $id]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute('admin_products');
            }
        }

        $formData = $form->createView();

        return [
            'title' => $title,
            'form'  => $formData,
        ];
    }

//    TODO: delete this controller
    /**
     * @param $id
     * @param Request $request
     * @Route("/attributes/{id}", name="admin_product_attributes",
     *     defaults={"id": 0},
     *     requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/products/form:attributes.html.twig")
     *
     * @return Response
     */
    public function productAttributesAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('AppBundle:Product')
            ->find($id);

        /** @var Category $category*/
        $category = $product->getCategory();
        $categoryId = $category->getId();
        $categoryTitle = $category->getTitle();
        $attributes = $category->getAttributes();

        $title = 'Edit product id: '.$id;

        $formBuilder = $this->createFormBuilder($product);
        $formBuilder->setAction($this->generateUrl('admin_product_attributes', ['id' => $id]));
        $formBuilder->setMethod('POST');

        foreach ($attributes as $attribute) {
            /** @var Attribute $attribute*/
            if ($attribute->getType() == 'select') {
                $formBuilder->add('attribute_'.$attribute->getId(), ChoiceType::class, [
                    'choices'           => $attribute->getOptionsAsArray(),
                    'label'             => $attribute->getName(),
                    'choices_as_values' => true,
                    'mapped'            => false,
                ]);
            }
            else {
                $formBuilder->add('attribute_'.$attribute->getId(), TextType::class, [
                    'label'  => $attribute->getName(),
                    'mapped' => false,
                ]);
            }
        }

        $formBuilder->add('save', SubmitType::class, array(
                    'label'     => 'Save',
                    'attr'      => [
                        'class' => 'btn btn-default'
                    ],
                )
            );
        $form = $formBuilder->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
//                $em->persist($product);
//                $em->flush();
//
//                return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
            }
        }

        $formData = $form->createView();

        return [
            'title' => $title,
            'form'  => $formData,
        ];
    }

//    TODO: rename, clean code, correct relation between product and attributeValue, because have an error on persist
    /**
     * @param $id
     * @param Request $request
     * @Route("/attributes_test/{id}", name="admin_product_attributes_test",
     *     defaults={"id": 0},
     *     requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/products/form:attributes.html.twig")
     *
     * @return Response
     */
    public function productAttributesTestAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('AppBundle:Product')
            ->find($id);

        /** @var Category $category*/
        /** @var Product $product*/
        $category = $product->getCategory();
        $categoryId = $category->getId();
        $categoryTitle = $category->getTitle();
        $attributes = $category->getAttributes();
        $attrValues = $product->getAttributeValues();

        if (count($attrValues) == 0) {
            foreach ($attributes as $attribute) {
                /** @var AttributeValue $attrValue */
                $attrValue = new AttributeValue();
                /** @var Attribute $attribute */
                $attrValue->setAttribute($attribute);
                $attrValue->setProduct($product);
                $product->addAttributeValue($attrValue);
            }
        }
        else $countAttr = count($attrValues);

        $title = 'Edit product id: '.$id;

//        TODO: create formType
        $formBuilder = $this->createFormBuilder($product);
        $formBuilder->setAction($this->generateUrl('admin_product_attributes_test', ['id' => $id]));
        $formBuilder->setMethod('POST');
        $formBuilder->add('attributeValues', CollectionType::class, array(
            'entry_type'   => ProductAttributesType::class,
            'label'        => 'Attributes'
        ));
        $formBuilder->add('save', SubmitType::class, array(
                'label'     => 'Save',
                'attr'      => [
                    'class' => 'btn btn-default'
                ],
            )
        );
        $form = $formBuilder->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
            }
        }

        $formData = $form->createView();

        return [
            'title' => $title,
            'form'  => $formData,
        ];
    }
}
