<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeOption
 *
 * @ORM\Table(name="attribute_option")
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
     */
    private $attributeOption;


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
}
