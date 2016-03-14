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
use Doctrine\Common\Collections\ArrayCollection;
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

        $form = $this->get('app.private_messages_service')->createPrivateMessagesForm('handle_received_messages', $allReceivedPrivateMessages)
            ->add('SetRead', SubmitType::class, ['label' => 'private_message.set_read'])
            ->add('SetUnread', SubmitType::class, ['label' => 'private_message.set_unread'])
            ->add('Delete', SubmitType::class, ['label' => 'private_message.delete']);

        return
            [
                'allReceivedPrivateMessages' => $allReceivedPrivateMessages,
                'form' => $form->createView(),
            ];
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

        $form = $this->get('app.private_messages_service')->createPrivateMessagesForm('handle_sent_messages', $allSentPrivateMessages)
            ->add('Delete', SubmitType::class, ['label' => 'private_message.delete']);

        return
            [
                'allSentPrivateMessages' => $allSentPrivateMessages,
                'form' => $form->createView(),
            ];
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
            $this->get('app.private_messages_service')->setPrivateMessagesRead([$privateMessage]);
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

    /**
     * @param Request $request
     * @Route("/received", name="handle_received_messages")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleReceivedMessagesAction(Request $request)
    {
        $form = $this->get('app.private_messages_service')->createPrivateMessagesForm('handle_received_messages', null)
            ->add('SetRead', SubmitType::class, ['label' => 'SetRead'])
            ->add('SetUnread', SubmitType::class, ['label' => 'SetUnread'])
            ->add('Delete', SubmitType::class, ['label' => 'Delete']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var arrayCollection $allReceivedPrivateMessages */
            $allReceivedPrivateMessages = $form->get('PrivateMessages')->getData();

            foreach ($allReceivedPrivateMessages as $receivedPrivateMessage) {
                $this->denyAccessUnlessGranted('edit_received_message', $receivedPrivateMessage);
            }

            if ($form->get('Delete')->isClicked()) {
                $this->get('app.private_messages_service')->deletePrivateMessagesFromReceived($allReceivedPrivateMessages->toArray());
            } elseif ($form->get('SetRead')->isClicked()) {
                $this->get('app.private_messages_service')->setPrivateMessagesRead($allReceivedPrivateMessages->toArray());
            } elseif ($form->get('SetUnread')->isClicked()) {
                $this->get('app.private_messages_service')->setPrivateMessagesUnread($allReceivedPrivateMessages->toArray());
            }
        }

        return $this->redirect($this->generateUrl('show_received_private_message'));
    }

    /**
     * @param Request $request
     * @Route("/sent", name="handle_sent_messages")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleSentMessagesAction(Request $request)
    {
        $form = $this->get('app.private_messages_service')->createPrivateMessagesForm('handle_sent_messages', null)
            ->add('Delete', SubmitType::class, ['label' => 'Delete']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var arrayCollection $allSentPrivateMessages */
            $allSentPrivateMessages = $form->get('PrivateMessages')->getData();

            foreach ($allSentPrivateMessages as $sentPrivateMessage) {
                $this->denyAccessUnlessGranted('edit_sent_message', $sentPrivateMessage);
            }

            if ($form->get('Delete')->isClicked()) {
                $this->get('app.private_messages_service')->deletePrivateMessagesFromSent($allSentPrivateMessages->toArray());
            }
        }

        return $this->redirect($this->generateUrl('show_sent_private_message'));
    }
}