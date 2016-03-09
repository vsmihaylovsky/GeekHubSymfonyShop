<?php

/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/9/16
 * Time: 9:02 PM
 */

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Twig_Extension;
use Twig_SimpleFunction;

class AppExtension extends Twig_Extension
{
    private $em;
    private $tokenStorage;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getUnreadPrivateMessagesCount', [$this, 'getUnreadPrivateMessagesCount']),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }

    public function getUnreadPrivateMessagesCount()
    {
        return $this->em->getRepository('AppBundle:PrivateMessage')->getUnreadPrivateMessagesCount($this->tokenStorage->getToken()->getUser());
    }
}