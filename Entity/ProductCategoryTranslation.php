<?php

namespace Sulmi\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sulmi\ProductBundle\Entity\ProductCategory;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Translations for Product category.
 * 
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @ORM\Entity
 * @ORM\Table(name="product_category_translations",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *         "locale", "object_id", "field"
 *     })}
 * )
 */
class ProductCategoryTranslation extends AbstractPersonalTranslation
{

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     */
    public function __construct($locale, $field, $value)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
    }

    /**
     * @ORM\ManyToOne(targetEntity="ProductCategory", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

}