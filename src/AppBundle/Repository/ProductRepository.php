<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{
    public function getProductsWithPictures()
    {
        return $query = $this->createQueryBuilder('p')
            ->select('p, pic')
            ->leftJoin('p.pictures', 'pic')
            ->where('p.deletedAt is null')
            ->andWhere('p.available = 1')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }

    public function getProductsWithCategory($search)
    {
        return $query = $this->createQueryBuilder('p')
            ->select('p, cat')
            ->leftJoin('p.category', 'cat')
            ->where('p.name like :product_name')
            ->andWhere('p.deletedAt is null')
            ->andWhere('p.available = 1')
            ->setParameters(['product_name' => "%$search%"])
            ->getQuery();
    }

    public function getProductsWithCategoryAdmin($search)
    {
        return $query = $this->createQueryBuilder('p')
            ->select('p, cat')
            ->leftJoin('p.category', 'cat')
            ->where('p.name like :product_name')
            ->setParameters(['product_name' => "%$search%"])
            ->getQuery();
    }

    public function getProductsFilteredByCategoryAdmin($category)
    {
        return $query = $this->createQueryBuilder('p')
            ->select('p, cat')
            ->leftJoin('p.category', 'cat')
            ->where('cat.slug = ?1')
            ->setParameter(1, $category)
            ->getQuery();
    }

    public function getProductWithJoins($slug)
    {
        return $this->createQueryBuilder('p')
            ->select('p, pic, cat, val, attr')
            ->leftJoin('p.pictures', 'pic')
            ->leftJoin('p.category', 'cat')
            ->leftJoin('p.attributeValues', 'val')
            ->leftJoin('val.attribute', 'attr')
            ->where('p.slug = ?1')
            ->setParameter(1, $slug)
            ->getQuery()
            ->getSingleResult();
    }

    public function getLatestProductsWithPictures($max = 9)
    {
        return $this->createQueryBuilder('p')
            ->select('p, pic')
            ->leftJoin('p.pictures', 'pic')
            ->orderBy('p.createdAt', 'DESC')
            ->where('p.deletedAt is null')
            ->andWhere('p.available = 1')
            ->setFirstResult(0)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    public function getFilteredProductsWithPictures($filter, $params)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p, pic, cat, opt')
            ->leftJoin('p.pictures', 'pic')
            ->leftJoin('p.category', 'cat')
            ->leftJoin('p.attributeValues', 'opt')
            ->where('p.deletedAt is null')
            ->andWhere('p.available = 1')
            ->orderBy('p.createdAt', 'DESC');

        switch ($filter) {
            case 'category':
                $query
                    ->andWhere('cat.slug = ?1')
                    ->setParameter(1, $params['category']);
                break;
            case 'filter':
                if (isset($params['category'])) {
                    $query->andWhere('cat.id = :category');
                    $paramsData['category'] = $params['category'];
                }
                if (isset($params['options']) && count($params['options']) > 0) {
                    foreach ($params['options'] as $attributeId => $attributeOptions) {
                        $productAlias = 'p' . $attributeId;
                        $attributesValuesAlias = 'a' . $attributeId;
                        $optionsConditions = '';
                        foreach ($attributeOptions as $optionId) {
                            if ($optionsConditions !== '') {
                                $optionsConditions .= ' OR ';
                            }
                            $optionsConditions .= "$attributesValuesAlias.attributeOption = :option$optionId";
                            $paramsData["option$optionId"] = $optionId;
                        }

                        $subQuery = $this->createQueryBuilder($productAlias)
                            ->select("$productAlias.id")
                            ->innerJoin("$productAlias.attributeValues", $attributesValuesAlias, 'WITH', $optionsConditions)
                        ->andWhere("$productAlias.id = p.id");
                        $query->andWhere($query->expr()->exists($subQuery->getDQL()));
                    }
                }
                if (isset($paramsData)) $query->setParameters($paramsData);
                break;
            case 'search':
                $query
                    ->andWhere('p.name like ?1')
                    ->setParameter(1, '%' . $params['name'] . '%');
                break;
        }

        return $query->getQuery();
    }
}
