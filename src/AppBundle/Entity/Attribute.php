<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attribute
 *
 * @ORM\Table(name="attributes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributeRepository")
 */
class Attribute
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="filterable", type="boolean")
     * @Assert\NotBlank()
     */
    private $filterable;

    /**
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="attributes")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="AttributeOption", mappedBy="attribute", cascade={"persist"})
     */
    private $options;

    /**
     * @ORM\OneToMany(targetEntity="AttributeValue", mappedBy="attribute")
     */
    private $attributeValues;

    private $params;

    /**
     * *******************************************************
     * Constructor
     * *******************************************************
     */
    public function __construct() {
        $this->options = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->params = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Attribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Attribute
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set filterable
     *
     * @param boolean $filterable
     *
     * @return Attribute
     */
    public function setFilterable($filterable)
    {
        $this->filterable = $filterable;

        return $this;
    }

    /**
     * Get filterable
     *
     * @return bool
     */
    public function getFilterable()
    {
        return $this->filterable;
    }

    /**
     * Add option
     *
     * @param \AppBundle\Entity\AttributeOption $option
     *
     * @return Attribute
     */
    public function addOption(AttributeOption $option)
    {
        $option->setAttribute($this);

        $this->options[] = $option;

        return $this;
    }

    /**
     * Remove option
     *
     * @param \AppBundle\Entity\AttributeOption $option
     */
    public function removeOption(AttributeOption $option)
    {
        $this->options->removeElement($option);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getOptionsAsArray()
    {
        $options = [];
        foreach($this->options as $option) {
            /** @var AttributeOption $option */
            $options[$option->getAttributeOption()] = $option->getId();
        }

        return $options;
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Attribute
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add attributeValue
     *
     * @param \AppBundle\Entity\AttributeValue $attributeValue
     *
     * @return Attribute
     */
    public function addAttributeValue(AttributeValue $attributeValue)
    {
        $this->attributeValues[] = $attributeValue;

        return $this;
    }

    /**
     * Remove attributeValue
     *
     * @param \AppBundle\Entity\AttributeValue $attributeValue
     */
    public function removeAttributeValue(AttributeValue $attributeValue)
    {
        $this->attributeValues->removeElement($attributeValue);
    }

    /**
     * Get attributeValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributeValues()
    {
        return $this->attributeValues;
    }



    /**
     * Add param
     *
     * @param \AppBundle\Entity\AttributeOption $param
     *
     * @return Attribute
     */
    public function addParam(AttributeOption $param)
    {
        $this->params[] = $param;

        return $this;
    }

    /**
     * Remove param
     *
     * @param \AppBundle\Entity\AttributeOption $param
     */
    public function removeParam(AttributeOption $param)
    {
        $this->params->removeElement($param);
    }

    /**
     * Get params
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParams()
    {
        return $this->params;
    }
}
