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
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
     * @param $id
     * @param Product $product
     * @param Request $request
     * @Route("/pictures/product/{id}", name="admin_product_pictures",
     *     requirements={
     *      "id": "\d+"
     *     })
     * @ParamConverter("product", class="AppBundle:Product")
     * @Template("AppBundle:admin/products/form:pictures.html.twig")
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction($id, Product $product, Request $request)
    {
        $formPicture = $this->createFormBuilder($product)
            ->setAction($this->generateUrl('admin_product_pictures', ['id' => $id]))
            ->setMethod('POST')
            ->add('pictures', CollectionType::class, array(
                    'entry_type' => PictureIsMainType::class,
                    'label'      => false,
                )
            )
            ->add('file', FileType::class, array(
                'attr'          => array('placeholder' => 'Picture'),
                'label'         => false,
                'mapped'        => false,
            ))
            ->add('update', SubmitType::class, array(
                    'label'     => 'Update',
                    'attr'      => [
                        'class' => 'btn btn-default'
                    ],
                )
            )
            ->add('upload', SubmitType::class, array(
                    'label'     => 'Upload',
                    'attr'      => [
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
            $formPicture->handleRequest($request);
            if ($formPicture->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);

                if ($formPicture->get('upload')->isClicked()) {
                    $file = $formPicture->get('file')->getData();
                    if ($file) {
                        $picture = new ProductPicture();
                        $picture->setProduct($product)->setFile($file);
                        $em->persist($picture);
                    }
                }

                $em->flush();

                return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
            }
        }

        return [
            'pictures' => $product->getPictures(),
            'isMain' => $formPicture->createView(),
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

                //return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
            }
        }

        return $this->redirectToRoute('admin_product_edit', ['action' => 'edit', 'id' => $id]);
    }

}