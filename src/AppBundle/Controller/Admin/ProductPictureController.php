<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPicture;
use AppBundle\Form\Type\PictureIsMainType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{_locale}/admin", defaults={"_locale": "%locale%"}, requirements={"_locale": "%app.locales%"})
 */
class ProductPictureController extends Controller
{
    /**
     * @Route("/pictures/product/{id}", name="admin_product_pictures",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @Template("AppBundle:admin/products/form:pictures.html.twig")
     */
    public function indexAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Product $product */
        $product = $em->getRepository('AppBundle:Product')
            ->find($id);

        $pictures = $product->getPictures();

        $formIsMainPicture = $this->createFormBuilder($product)
            ->setAction($this->generateUrl('admin_product_pictures', ['id' => $id]))
            ->setMethod('POST')
            ->add('pictures', CollectionType::class, array(
                    'entry_type' => PictureIsMainType::class,
                    'label' => false,
                )
            )
            ->add('update', SubmitType::class, array(
                    'label' => 'Update',
                    'attr' => [
                        'class' => 'btn btn-default'
                    ],
                )
            )
            ->getForm();


        $formDelete = $this->createForm(FormType::class, null, [
            'method' => Request::METHOD_POST,
        ])
            ->add('delete', SubmitType::class, [
                    'label' => 'delete',
                    'attr' => ['class' => 'btn btn-danger'],
                ]
            );


        if ($request->getMethod() == 'POST') {
            $formIsMainPicture->handleRequest($request);
            if ($formIsMainPicture->isValid()) {
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
            }
        }

        return [
            'pictures' => $pictures,
            'isMain' => $formIsMainPicture->createView(),
            'delete' => $formDelete->createView(),
        ];
    }

    /**
     * @param ProductPicture $picture
     * @param Request $request
     * @Route("/picture/delete/{id}", name="admin_product_picture_delete",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @ParamConverter("picture", class="AppBundle:ProductPicture")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePictureAction(ProductPicture $picture, Request $request)
    {
        $id = $picture->getProduct()->getId();

        $formDelete = $this->createForm(FormType::class, null, [
            'method' => Request::METHOD_POST,
        ])
            ->add('delete', SubmitType::class, [
                'label' => 'delete',
                'attr' => ['class' => 'btn btn-danger'],
            ]);

        if ($request->getMethod() == 'POST') {
            $formDelete->handleRequest($request);
            if ($formDelete->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($picture);
                $em->flush();

                return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
            }
        }

        return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
    }

}