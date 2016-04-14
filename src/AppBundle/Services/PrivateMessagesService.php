<?php

/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/13/16
 * Time: 3:01 PM
 */

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;
use AppBundle\Entity\PrivateMessage;

class PrivateMessagesService
{
    private $em;
    private $formFactory;
    private $router;

    /**
     * PrivateMessagesService constructor.
     * @param EntityManager $entityManager
     * @param FormFactory $formFactory
     * @param Router $router
     */
    public function __construct(EntityManager $entityManager, FormFactory $formFactory, Router $router)
    {
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function createPrivateMessagesForm($actionRoute, $privateMessages)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $this->formFactory->createBuilder();

        return $formBuilder
            ->setAction($this->router->generate($actionRoute))
            ->setMethod('POST')
            ->add('PrivateMessages', EntityType::class, [
                'class' => 'AppBundle:PrivateMessage',
                'choices' => $privateMessages,
                'choice_label' => null,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->getForm();
    }

    /**
     * @param array $privateMessages
     */
    public function deletePrivateMessagesFromReceived(array $privateMessages)
    {
        /** @var PrivateMessage $privateMessage */
        foreach ($privateMessages as $privateMessage) {
            $privateMessage->setDeletedFromReceived(true);
        }
        $this->em->flush();
    }

    /**
     * @param array $privateMessages
     */
    public function deletePrivateMessagesFromSent(array $privateMessages)
    {
        /** @var PrivateMessage $privateMessage */
        foreach ($privateMessages as $privateMessage) {
            $privateMessage->setDeletedFromSent(true);
        }
        $this->em->flush();
    }

    /**
     * @param array $privateMessages
     */
    public function setPrivateMessagesRead(array $privateMessages)
    {
        /** @var PrivateMessage $privateMessage */
        foreach ($privateMessages as $privateMessage) {
            $privateMessage->setIsViewed(true);
        }
        $this->em->flush();
    }

    /**
     * @param array $privateMessages
     */
    public function setPrivateMessagesUnread(array $privateMessages)
    {
        /** @var PrivateMessage $privateMessage */
        foreach ($privateMessages as $privateMessage) {
            $privateMessage->setIsViewed(false);
        }
        $this->em->flush();
    }
}