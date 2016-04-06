<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 4/5/16
 * Time: 2:10 PM
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\Type\NewsletterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Newsletter;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/newsletter")
 */
class NewsletterController extends Controller
{
    /**
     * @return array
     * @Route("/", name="admin_newsletters")
     * @Method("GET")
     * @Template("AppBundle:admin/newsletter:list.html.twig")
     */
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $newsletters = $em->getRepository('AppBundle:Newsletter')->findBy([], ['createdAt' => 'DESC']);

        $deleteForms = $this->get('app.delete_form_service')->getEntitiesDeleteForms($newsletters, 'delete_newsletter');

        return
            [
                'newsletters' => $newsletters,
                'deleteForms' => $deleteForms,
            ];
    }

    /**
     * @return array
     * @Route("/new", name="new_newsletter")
     * @Method("GET")
     * @Template("AppBundle:admin/newsletter:form.html.twig")
     */
    public function newAction()
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter, [
            'action' => $this->generateUrl('create_newsletter'),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'table.create']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Newsletter $newsletter
     * @return array
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="edit_newsletter")
     * @ParamConverter("newsletter", class="AppBundle:Newsletter")
     * @Method("GET")
     * @Template("AppBundle:admin/newsletter:form.html.twig")
     */
    public function editAction(Newsletter $newsletter)
    {
        $form = $this->createForm(NewsletterType::class, $newsletter, [
            'action' => $this->generateUrl('update_newsletter', ['id' => $newsletter->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'table.update']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/", name="create_newsletter")
     * @Method("POST")
     * @Template("AppBundle:admin/newsletter:form.html.twig")
     */
    public function createAction(Request $request)
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter, [
            'action' => $this->generateUrl('create_newsletter'),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'table.create']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_newsletters'));
        }
        
        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param Newsletter $newsletter
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", requirements={"id": "\d+"}, name="update_newsletter")
     * @ParamConverter("newsletter", class="AppBundle:Newsletter")
     * @Method("PUT")
     * @Template("AppBundle:admin/newsletter:form.html.twig")
     */
    public function updateAction(Request $request, Newsletter $newsletter)
    {
        $form = $this->createForm(NewsletterType::class, $newsletter, [
            'action' => $this->generateUrl('update_newsletter', ['id' => $newsletter->getId()]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'table.update']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_newsletters'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param Newsletter $newsletter
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", requirements={"id": "\d+"}, name="delete_newsletter")
     * @ParamConverter("newsletter", class="AppBundle:Newsletter")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Newsletter $newsletter)
    {
        $form = $this->get('app.delete_form_service')->createEntityDeleteForm($newsletter->getId(), 'delete_newsletter');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($newsletter);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_newsletters'));
    }

    /**
     * @param Newsletter $newsletter
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/show/{id}", requirements={"id": "\d+"}, name="show_newsletter")
     * @ParamConverter("newsletter", class="AppBundle:Newsletter")
     * @Method("GET")
     * @Template("AppBundle:admin/newsletter:send.html.twig")
     */
    public function showAction(Newsletter $newsletter)
    {
        $form = $this->get('app.newsletter_service')->getSendNewsletterForm($newsletter);

        return
            [
                'newsletter' => $newsletter,
                'form' => $form->createView(),
            ];
    }

    /**
     * @param Request $request
     * @param Newsletter $newsletter
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/send/{id}", requirements={"id": "\d+"}, name="send_newsletter")
     * @ParamConverter("newsletter", class="AppBundle:Newsletter")
     * @Method("POST")
     */
    public function sendAction(Request $request, Newsletter $newsletter)
    {
        $form = $this->get('app.newsletter_service')->getSendNewsletterForm($newsletter);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $users = [];
            
            if ($form->get('sendTest')->isClicked()) {
                $user = new User();
                $user->setUsername($this->getUser()->getUsername());
                $user->setEmail($form->get('send_test_email')->getData());
                $users = [$user];
            } elseif ($form->get('send')->isClicked()) {
                $em = $this->getDoctrine()->getManager();
                $users = $em->getRepository('AppBundle:User')->findBy(['subscribe' => true]);
            };

            $this->get('app.newsletter_service')->sendNewsletter($newsletter, $users);
        }

        return $this->redirect($this->generateUrl('admin_newsletters'));
    }
}