<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, updatable=true, separator="_")
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="type", type="string", length=20)
//     */
//    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

//    /**
//     * @ORM\OneToMany(targetEntity="Product", mappedBy="parent")
//     */
//    private $children;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="Product", inversedBy="children")
//     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
//     */
//    private $parent;

//    /**
//     * @ORM\ManyToMany(targetEntity="Attribute", inversedBy="products")
//     * @ORM\JoinTable(name="products_attributes")
//     */
//    private $attributes;

    /**
     * @ORM\OneToMany(targetEntity="AttributeValue", mappedBy="product", cascade={"persist"})
     */
    private $attributeValues;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="qty", type="integer")
     */
    private $qty;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="priceSpecial", type="float")
     */
    private $priceSpecial;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @var bool
     *
     * @ORM\Column(name="available", type="boolean")
     */
    private $available;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="change", field={"name", "description", "price", "priceSpecial", "qty"})
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * *******************************************************
     * Constructor
     * *******************************************************
     */
    public function __construct() {
//        $this->children = new ArrayCollection();
//        $this->attributes = new ArrayCollection();
        $this->attributeValues = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
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
     * Set slug
     *
     * @param string $slug
     * @return Product
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

//    /**
//     * Set type
//     *
//     * @param string $type
//     * @return Product
//     */
//    public function setType($type)
//    {
//        $this->type = $type;
//
//        return $this;
//    }
//
//    /**
//     * Get type
//     *
//     * @return string
//     */
//    public function getType()
//    {
//        return $this->type;
//    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set qty
     *
     * @param integer $qty
     * @return Product
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return integer 
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set priceSpecial
     *
     * @param float $priceSpecial
     * @return Product
     */
    public function setPriceSpecial($priceSpecial)
    {
        $this->priceSpecial = $priceSpecial;

        return $this;
    }

    /**
     * Get priceSpecial
     *
     * @return float 
     */
    public function getPriceSpecial()
    {
        return $this->priceSpecial;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return Product
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set available
     *
     * @param boolean $available
     * @return Product
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Product
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Product
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Product
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

//    /**
//     * Add children
//     *
//     * @param \AppBundle\Entity\Product $children
//     * @return Product
//     */
//    public function addChild(Product $children)
//    {
//        $this->children[] = $children;
//
//        return $this;
//    }
//
//    /**
//     * Remove children
//     *
//     * @param \AppBundle\Entity\Product $children
//     */
//    public function removeChild(Product $children)
//    {
//        $this->children->removeElement($children);
//    }
//
//    /**
//     * Get children
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getChildren()
//    {
//        return $this->children;
//    }
//
//    /**
//     * Set parent
//     *
//     * @param \AppBundle\Entity\Product $parent
//     * @return Product
//     */
//    public function setParent(Product $parent = null)
//    {
//        $this->parent = $parent;
//
//        return $this;
//    }
//
//    /**
//     * Get parent
//     *
//     * @return \AppBundle\Entity\Product
//     */
//    public function getParent()
//    {
//        return $this->parent;
//    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Product
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
     * Add attributeValue
     *
     * @param \AppBundle\Entity\AttributeValue $attributeValue
     *
     * @return Product
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

//    /**
//     * Add attribute
//     *
//     * @param \AppBundle\Entity\Attribute $attribute
//     *
//     * @return Product
//     */
//    public function addAttribute(Attribute $attribute)
//    {
//        $this->attributes[] = $attribute;
//
//        return $this;
//    }
//
//    /**
//     * Remove attribute
//     *
//     * @param \AppBundle\Entity\Attribute $attribute
//     */
//    public function removeAttribute(Attribute $attribute)
//    {
//        $this->attributes->removeElement($attribute);
//    }
//
//    /**
//     * Get attributes
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getAttributes()
//    {
//        return $this->attributes;
//    }
}