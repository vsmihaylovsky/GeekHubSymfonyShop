<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Product;
use AppBundle\Form\Type\DeleteType;
use AppBundle\Form\Type\ProductType;
use AppBundle\Form\Type\ProductAttributesType;
use Faker\Provider\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
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
            'delete' => $this->createForm(DeleteType::class, null, [])->createView(),
        ];
    }

    /**
     * @param $filter
     * @param $param
     * @return Response
     * @Route("/products/{filter}/{param}", name="admin_products_filtered",
     *     defaults={"filter": "none", "param": "none"},
     *     requirements={
     *          "filter": "none|category"
     *     })
     * @Template("AppBundle:admin/products:products.html.twig")
     */
    public function productsFilteredAction($filter, $param, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')
            ->getFilteredProductsWithPictures($filter, $param)
            ->getResult();

        return [
            'products'  => $products,
            'delete' => $this->createForm(DeleteType::class, null, [])->createView(),
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
            'delete' => $this->createForm(DeleteType::class, null, [])->createView(),
        ];
    }

    /**
     * @param $id
     * @param Product $product
     * @param Request $request
     * @Route("/attributes/{id}", name="admin_product_attributes",
     *     defaults={"id": 0},
     *     requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/products/form:attributes.html.twig")
     * @ParamConverter("picture", class="AppBundle:Product")
     *
     * @return Response
     */
    public function productAttributesAction($id, Product $product, Request $request)
    {
        $title = 'Edit product id: '.$id;

        $product = $this->container->get('app.attributes_handler')->updateAttributeSet($product);

        $form = $this->createFormBuilder($product)
            ->setAction($this->generateUrl('admin_product_attributes', ['id' => $id]))
            ->setMethod('POST')
            ->add('attributeValues', CollectionType::class, array(
                'entry_type'   => ProductAttributesType::class,
                'label'        => 'Attributes',
            ))
            ->add('save', SubmitType::class, array(
                'label'     => 'Save',
                'attr'      => [
                    'class' => 'btn btn-default'
                ],
            ))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
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

    /**
     * @param Product $product
     * @param Request $request
     * @Route("/product/delete/{id}", name="admin_product_delete",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Method({"POST"})
     * @ParamConverter("Product", class="AppBundle:Product")
     * @Template("AppBundle:admin:messages.html.twig")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProductAction(Product $product, Request $request)
    {
        $product->setDeletedAt(new \DateTime('now'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('admin_products');
    }
}
