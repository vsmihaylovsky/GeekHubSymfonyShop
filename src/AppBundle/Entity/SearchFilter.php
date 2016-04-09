<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class SearchFilter
{
    private $category;
    private $filters;
    private $sale;
    private $sort;
    private $directions;

    /**
     * *******************************************************
     * Constructor
     * *******************************************************
     */
    public function __construct() {
        $this->filters = new ArrayCollection();
        $this->directions = new ArrayCollection();
        $this
            ->addDirection('up')
            ->addDirection('down');
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return SearchFilter
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add filter
     *
     * @param \AppBundle\Entity\Attribute $filter
     *
     * @return SearchFilter
     */
    public function addFilter(Attribute $filter)
    {
        $this->filters[$filter->getId()] = $filter;

        return $this;
    }

    /**
     * Remove filter
     *
     * @param \AppBundle\Entity\Attribute $filter
     */
    public function removeFilter(Attribute $filter)
    {
        $this->filters->removeElement($filter);
    }

    /**
     * Get filters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    public function setSale($sale = null)
    {
        $this->sale = $sale;

        return $this;
    }

    public function getSale()
    {
        return $this->sale;
    }

    public function setSort($sort = null)
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function addDirection($direction)
    {
        $this->directions[$direction] = $direction;

        return $this;
    }

    public function removeDirection($direction)
    {
        $this->directions->removeElement($direction);
    }

    public function getDirections()
    {
        return $this->directions;
    }
}
