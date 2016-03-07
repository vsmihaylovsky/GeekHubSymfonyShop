<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    public function getFirstLevel()
    {
        return $this->createQueryBuilder('c')
            ->select('c, children')
            ->leftJoin('c.children', 'children')
            ->where('c.parent is null')
            ->andWhere('c.hasProducts is null')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getLastLevel()
    {
        return $this->createQueryBuilder('c')
            ->where('c.hasChildren is null')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}