<?php

/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/9/16
 * Time: 9:02 PM
 */

namespace AppBundle\Twig;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPicture;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_Environment;

class AppExtension extends Twig_Extension
{
    private $em;
    private $tokenStorage;
    private $comingSoonPicture;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, $comingSoonPicture)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->comingSoonPicture = $comingSoonPicture;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getUnreadPrivateMessagesCount', [$this, 'getUnreadPrivateMessagesCount']),
            new Twig_SimpleFunction('categories',
                    [$this, 'getCategories'],
                    ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new Twig_SimpleFunction('getProductRating', [$this, 'getProductRating']),
            new Twig_SimpleFunction('getProductReviewsCount', [$this, 'getProductReviewsCount']),
            new Twig_SimpleFunction('getProductMainPicture', [$this, 'getProductMainPicture']),
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

    public function getProductReviewsCount($slug)
    {
        return $this->em->getRepository('AppBundle:Review')->getProductReviewsCount($slug);
    }

    public function getProductRating($slug)
    {
        return $this->em->getRepository('AppBundle:Review')->getProductRating($slug);
    }

    public function getProductMainPicture(Product $product)
    {
        if ($product->getPictures()->count() > 0) {
            $mainPicture = $product->getPictures()->get(0)->getWebPath();

            /** @var ProductPicture $picture */
            foreach ($product->getPictures() as $picture ) {
                if ($picture->getIsMain()) {
                    $mainPicture = $picture->getWebPath();
                }
            }

            return $mainPicture;
        } else {
            return $this->comingSoonPicture;
        }
    }
}