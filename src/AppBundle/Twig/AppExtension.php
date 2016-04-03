<?php

/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/9/16
 * Time: 9:02 PM
 */

namespace AppBundle\Twig;

use AppBundle\Services\SearchFormService;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_Environment;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

class AppExtension extends Twig_Extension
{
    private $em;
    private $tokenStorage;
    private $formFactory;
    private $router;
    private $search;

    public function __construct(EntityManager $entityManager,
                                TokenStorage $tokenStorage,
                                FormFactory $formFactory,
                                Router $router,
                                SearchFormService $search)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->search = $search;
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
            new Twig_SimpleFunction('categoryFilters',
                [$this, 'getCategoryFilters'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new Twig_SimpleFunction('searchForm',
                [$this, 'getSearchForm'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
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

    public function getCategories(Twig_Environment $twig, $slug)
    {
        $currentCategory = $this->em->getRepository('AppBundle:Category')->findOneBy(['slug' => $slug]);
        if($currentCategory instanceof Category) {
            $currentSlug = $slug;
            $parentCategory = $currentCategory->getParent();
            if($parentCategory instanceof Category) {
                $parentSlug = $parentCategory->getSlug();
            }
            else $parentSlug = '';
        }
        else {
            $parentSlug = '';
            $currentSlug = '';
        }

        return $twig->render(
            'AppBundle:shop:default/widgetCategories.html.twig', [
                'currentCategory' => [
                    'parentSlug' => $parentSlug,
                    'currentSlug' => $currentSlug,
                ],
                'categories' => $this->em->getRepository('AppBundle:Category')->getFirstLevel(),
            ]
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

    public function getCategoryFilters(Twig_Environment $twig, $slug)
    {
        $filterForm = $this->search->getFilterForm($slug);

        return $twig->render(
            'AppBundle:shop:default/widgetFilters.html.twig', [
                'formFilter' => $filterForm ? $filterForm->createView() : '',
            ]
        );
    }

    public function getSearchForm(Twig_Environment $twig)
    {
        return $twig->render(
            'AppBundle:shop:default/widgetSearch.html.twig', [
                'search' => $this->search->getSearchForm()->createView(),
            ]
        );
    }
}