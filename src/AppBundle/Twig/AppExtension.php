<?php

/**
 * Created by PhpStorm.
 * User: vad
 * Date: 3/9/16
 * Time: 9:02 PM
 */

namespace AppBundle\Twig;

use AppBundle\Services\ProductSortingService;
use AppBundle\Services\SearchFormService;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPicture;
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
    const SORT_KEY = 'product_sorting';
    const DEFAULT_SORT_INDEX = 'rating';
    const SORT_TYPES =
        [
            'cheap' => 'product_sort.cheap',
            'expensive' => 'product_sort.expensive',
            'rating' => 'product_sort.rating',
            'novelty' => 'product_sort.novelty',
        ];

    private $em;
    private $tokenStorage;
    private $comingSoonPicture;
    private $formFactory;
    private $router;
    private $search;
    private $productSortingService;

    public function __construct(EntityManager $entityManager,
                                TokenStorage $tokenStorage,
                                FormFactory $formFactory,
                                Router $router,
                                SearchFormService $search,
                                ProductSortingService $productSortingService,
                                $comingSoonPicture)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->comingSoonPicture = $comingSoonPicture;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->search = $search;
        $this->productSortingService = $productSortingService;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getUnreadPrivateMessagesCount', [$this, 'getUnreadPrivateMessagesCount']),
            new Twig_SimpleFunction('categories',
                    [$this, 'getCategories'],
                    ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new Twig_SimpleFunction('getProductMainPicture', [$this, 'getProductMainPicture']),
            new Twig_SimpleFunction('categoryFilters',
                [$this, 'getCategoryFilters'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new Twig_SimpleFunction('searchForm',
                [$this, 'getSearchForm'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            ),
            new Twig_SimpleFunction('productSorting', [$this, 'getProductSorting']),
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

    public function getCategoryFilters(Twig_Environment $twig, $slug)
    {
        $filterFormData = $this->search->getFilterForm($slug);

        return $twig->render(
            'AppBundle:shop:default/widgetFilters.html.twig', [
                'formFilter' => $filterFormData ? $filterFormData['form']->createView() : '',
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

    public function getProductSorting()
    {
        return $this->productSortingService->getProductSorting();
    }
}