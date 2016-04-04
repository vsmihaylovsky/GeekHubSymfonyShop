<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class SearchFilter
{
    private $category;
    private $filters;

    /**
     * *******************************************************
     * Constructor
     * *******************************************************
     */
    public function __construct() {
        $this->filters = new ArrayCollection();
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
}
