<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/3/16
 * Time: 9:41 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\PrivateMessage;
use AppBundle\Entity\User;
use AppBundle\Form\Type\PrivateMessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/private-message")
 */
class PrivateMessageController extends Controller
{
    /**
     * @Route("/new/{id}", requirements={"id": "\d+"}, name="new_private_message")
     * @ParamConverter("recipient", class="AppBundle:User")
     * @Method("GET")
     * @Template("AppBundle:PrivateMessage:form.html.twig")
     * @param User $recipient
     * @return array
     */
    public function newAction(User $recipient)
    {
        $privateMessage = new PrivateMessage();
        $form = $this->createForm(PrivateMessageType::class, $privateMessage, [
            'action' => $this->generateUrl('create_private_message', ['id' => $recipient->getId()]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Send']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param User $recipient
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", requirements={"id": "\d+"}, name="create_private_message")
     * @ParamConverter("recipient", class="AppBundle:User")
     * @Method("POST")
     * @Template("AppBundle:PrivateMessage:form.html.twig")
     */
    public function createAction(Request $request, User $recipient)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $sender = $this->getUser();

        $privateMessage = new PrivateMessage();
        $privateMessage->setSender($sender);
        $privateMessage->setRecipient($recipient);

        $form = $this->createForm(PrivateMessageType::class, $privateMessage, [
            'action' => $this->generateUrl('create_private_message', ['id' => $recipient->getId()]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Send']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($privateMessage);
            $em->flush();

            return $this->redirect($this->generateUrl('homepage'));
        }

        return ['form' => $form->createView()];
    }
}