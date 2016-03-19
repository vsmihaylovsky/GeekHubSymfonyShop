<?php
namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    private $parentTemp;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\Column(name="has_children", type="integer", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $hasChildren;

    /**
     * @ORM\Column(name="has_products", type="integer", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $hasProducts;

    /**
     * @ORM\Column(name="title", type="string", length=64)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="Attribute", inversedBy="categories")
     * @ORM\JoinTable(name="categories_attributes")
     */
    private $attributes;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category", cascade={"persist"})
     */
    private $products;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * *******************************************************
     * Constructor
     * *******************************************************
     */
    public function __construct() {
        $this->children = new ArrayCollection();
        $this->attributes = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set hasChildren
     *
     * @param integer $hasChildren
     *
     * @return Category
     */
    public function setHasChildren($hasChildren)
    {
        $this->hasChildren = $hasChildren;

        return $this;
    }

    /**
     * Get hasChildren
     *
     * @return integer
     */
    public function getHasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Category
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
     * Set parent
     *
     * @param \AppBundle\Entity\Category $parent
     *
     * @return Category
     */
    public function setParent(Category $parent = null)
    {
        if (isset($this->parent)) {
            $this->parentTemp = $this->parent;
        }
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Category
     */
    public function getParentTemp()
    {
        return $this->parentTemp;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Category $child
     */
    public function removeChild(Category $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add attribute
     *
     * @param \AppBundle\Entity\Attribute $attribute
     *
     * @return Category
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param \AppBundle\Entity\Attribute $attribute
     */
    public function removeAttribute(Attribute $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return Category
     */
    public function addProduct(Product $product)
    {
        $product->setCategory($this);
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Product $product
     */
    public function removeProduct(Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set hasProducts
     *
     * @param integer $hasProducts
     *
     * @return Category
     */
    public function setHasProducts($hasProducts)
    {
        $this->hasProducts = $hasProducts;

        return $this;
    }

    /**
     * Get hasProducts
     *
     * @return integer
     */
    public function getHasProducts()
    {
        return $this->hasProducts;
    }
}
