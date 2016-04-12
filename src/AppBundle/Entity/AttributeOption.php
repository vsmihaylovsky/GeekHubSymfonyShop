<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AttributeOption
 *
 * @ORM\Table(name="attribute_options")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributeOptionRepository")
 */
class AttributeOption
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
     * @ORM\ManyToOne(targetEntity="Attribute", inversedBy="options")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    private $attribute;

    /**
     * @var string
     *
     * @ORM\Column(name="attributeOption", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $attributeOption;

    /**
     * @ORM\OneToMany(targetEntity="AttributeValue", mappedBy="attributeOption")
     */
    private $attributeValues;

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
     * Set attributeOption
     *
     * @param string $attributeOption
     *
     * @return AttributeOption
     */
    public function setAttributeOption($attributeOption)
    {
        $this->attributeOption = $attributeOption;

        return $this;
    }

    /**
     * Get attributeOption
     *
     * @return string
     */
    public function getAttributeOption()
    {
        return $this->attributeOption;
    }

    /**
     * Set attribute
     *
     * @param \AppBundle\Entity\Attribute $attribute
     *
     * @return AttributeOption
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
     * Constructor
     */
    public function __construct()
    {
        $this->attributeValues = new ArrayCollection();
    }

    /**
     * Add attributeValue
     *
     * @param \AppBundle\Entity\AttributeValue $attributeValue
     *
     * @return AttributeOption
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
}
