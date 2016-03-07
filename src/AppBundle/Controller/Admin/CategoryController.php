<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\CategoryType;
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
class CategoryController extends Controller
{
    /**
     * @param Request $request
     * @Route("/categories", name="admin_categories")
     * @Template("AppBundle:admin/categories:categories.html.twig")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category')
            ->getFirstLevel();

        return [
            'categories'  => $categories,
        ];
    }

    /**
     * @param $id
     * @param $action
     * @param Request $request
     * @Route("/category/{action}/{id}", name="admin_category_edit",
     *     defaults={"id": 0, "action": "new"},
     *     requirements={
     *      "action": "new|edit",
     *      "id": "\d+"
     *     })
     * @Method({"GET", "POST"})
     * @Template("AppBundle:admin/categories/form:category.html.twig")
     *
     * @return Response
     */
    public function categoryEditAction($id, $action, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($action == "edit") {
            $category = $em->getRepository('AppBundle:Category')
                ->find($id);
            $title = 'Edit category id: '.$id;
        }
        else {
            $category = new Category();
            $title = 'Create new category';
        }

        $form = $this->createForm(CategoryType::class, $category, [
            'em' => $em,
            'action' => $this->generateUrl('admin_category_edit', ['action' => $action, 'id' => $id]),
            'method' => Request::METHOD_POST,
        ])
            ->add('save', SubmitType::class, array('label' => 'Save'));

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($category);
                $em->flush();

                return $this->redirectToRoute('admin_categories');
            }
        }

        $formData = $form->createView();

        return [
            'title' => $title,
            'form'  => $formData,
        ];
    }



}
