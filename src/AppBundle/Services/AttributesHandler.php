<?php
namespace AppBundle\Services;

use AppBundle\Entity\Product;
use AppBundle\Entity\AttributeValue;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AttributesHandler
{
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function updateAttributeSet(Product $product)
    {
        $attributes = $product->getCategory()->getAttributes();
        $attributesCloned = clone $attributes;
        $attrValues = $product->getAttributeValues();

        if ($attrValues->count() > 0) {
            $em = $this->doctrine->getManager();
            foreach ($attrValues as $attrValue) {
                $attribute = $attrValue->getAttribute();
                if ($attributes->contains($attribute)) {
                    $attributesCloned->removeElement($attribute);
                } else {
                    $product->removeAttributeValue($attrValue);
                    $em->remove($attrValue);
                }
            }
            $em->flush();
        }
        if ($attributesCloned->count() > 0) {
            foreach ($attributesCloned as $attribute) {
                $attrValue = new AttributeValue();
                $attrValue->setProduct($product)->setAttribute($attribute);
                $product->addAttributeValue($attrValue);
            }
        }

        return $product;
    }
}