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
     * @Template("AppBundle:shop/PrivateMessage:form.html.twig")
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
            ->add('save', SubmitType::class, ['label' => 'private_message.send']);

        return
            [
                'form' => $form->createView(),
                'recipient' => $recipient
            ];
    }

    /**
     * @param Request $request
     * @param User $recipient
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", requirements={"id": "\d+"}, name="create_private_message")
     * @ParamConverter("recipient", class="AppBundle:User")
     * @Method("POST")
     * @Template("AppBundle:shop/PrivateMessage:form.html.twig")
     */
    public function createAction(Request $request, User $recipient)
    {
        $sender = $this->getUser();

        $privateMessage = new PrivateMessage();
        $privateMessage->setSender($sender);
        $privateMessage->setRecipient($recipient);

        $form = $this->createForm(PrivateMessageType::class, $privateMessage, [
            'action' => $this->generateUrl('create_private_message', ['id' => $recipient->getId()]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'private_message.send']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($privateMessage);
            $em->flush();

            return $this->redirect($this->generateUrl('show_sent_private_message'));
        }

        return
            [
                'form' => $form->createView(),
                'recipient' => $recipient
            ];
    }

    /**
     * @Route("/received", name="show_received_private_message")
     * @Method("GET")
     * @Template("AppBundle:shop/PrivateMessage:received.html.twig")
     * @return array
     */
    public function showReceivedAction()
    {
        $recipient = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $allReceivedPrivateMessages = $em->getRepository('AppBundle:PrivateMessage')->getAllReceivedPrivateMessages($recipient);

        return ['allReceivedPrivateMessages' => $allReceivedPrivateMessages];
    }

    /**
     * @Route("/sent", name="show_sent_private_message")
     * @Method("GET")
     * @Template("AppBundle:shop/PrivateMessage:sent.html.twig")
     * @return array
     */
    public function showSentAction()
    {
        $sender = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $allSentPrivateMessages = $em->getRepository('AppBundle:PrivateMessage')->getAllSentPrivateMessages($sender);

        return ['allSentPrivateMessages' => $allSentPrivateMessages];
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, name="show_private_message")
     * @ParamConverter("privateMessage", class="AppBundle:PrivateMessage")
     * @Method("GET")
     * @Template("AppBundle:shop/PrivateMessage:show.html.twig")
     * @param PrivateMessage $privateMessage
     * @return array
     */
    public function showAction(PrivateMessage $privateMessage)
    {
        $this->denyAccessUnlessGranted('read_message', $privateMessage);

        if ((!$privateMessage->getIsViewed()) && ($this->getUser() === $privateMessage->getRecipient())) {
            $privateMessage->setIsViewed(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        $newPrivateMessage = new PrivateMessage();
        $form = $this->createForm(PrivateMessageType::class, $newPrivateMessage, [
            'action' => $this->generateUrl('create_private_message', ['id' => $privateMessage->getRecipient()->getId()]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'private_message.send']);

        return
            [
                'privateMessage' => $privateMessage,
                'form' => $form->createView(),
            ];
    }
}