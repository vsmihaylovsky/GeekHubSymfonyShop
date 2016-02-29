<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeValue
 *
 * @ORM\Table(name="attribute_values")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributeValueRepository")
 */
class AttributeValue
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
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="attributeValues")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity="Attribute")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    private $attribute;

    /**
     * @ORM\OneToOne(targetEntity="AttributeOption")
     * @ORM\JoinColumn(name="attribute_option_id", referencedColumnName="id", nullable=true)
     */
    private $attributeOption;

    /**
     * @var string
     *
     * @ORM\Column(name="attributeValue", type="string", length=255)
     */
    private $attributeValue;

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
     * Set attributeValue
     *
     * @param string $attributeValue
     *
     * @return AttributeValue
     */
    public function setAttributeValue($attributeValue)
    {
        if ($attributeValue instanceof AttributeOption) {
            $this->attributeValue = $attributeValue->getAttributeOption();
            $this->attributeOption = $attributeValue;
        }
        else {
            $this->attributeValue = $attributeValue;
        }

        return $this;
    }

    /**
     * Get attributeValue
     *
     * @return string
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return AttributeValue
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set attribute
     *
     * @param \AppBundle\Entity\Attribute $attribute
     *
     * @return AttributeValue
     */
    public function setAttribute(Attribute $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return \AppBundle\Entity\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set attributeOption
     *
     * @param \AppBundle\Entity\AttributeOption $attributeOption
     *
     * @return AttributeValue
     */
    public function setAttributeOption(AttributeOption $attributeOption = null)
    {
        $this->attributeOption = $attributeOption;

        return $this;
    }

    /**
     * Get attributeOption
     *
     * @return \AppBundle\Entity\AttributeOption
     */
    public function getAttributeOption()
    {
        return $this->attributeOption;
    }
}
