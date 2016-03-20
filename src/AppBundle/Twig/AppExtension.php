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
use Twig_Environment;

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
            new Twig_SimpleFunction('categories',
                    [$this, 'getCategories'],
                    ['needs_environment' => true, 'is_safe' => ['html']]
            )
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

    public function getCategories(Twig_Environment $twig)
    {
        return $twig->render(
            'AppBundle:shop:default/widgetCategories.html.twig',
            array(
                'categories' => $this->em->getRepository('AppBundle:Category')->getFirstLevel(),
            )
        );
    }
}