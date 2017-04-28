<?php

namespace Sulmi\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use Sulmi\ProductBundle\Repository\ProductRepository;

/**
 * Product entity
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="Sulmi\ProductBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{

    /**
     * Id unique firld
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\OrderBy({"id" = "desc", "name" = "asc"})
     */
    private $id;

    /**
     * @var string Name of product
     * 
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3, max = 64)
     */
    private $name;

    /**
     * @ORM\Column(type="float", precision=2, options={"default"=0})
     * @Assert\NotBlank()
     * It can be a string, number or object.
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\LessThanOrEqual(1000000)
     * @var type decimal Product proce
     */
    private $price;

    /**
     * @var string Slug name for Product 
     * 
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=64, unique=true)
     */
    private $slug;

    /**
     * @var string Description for Product 
     * 
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * It represents the relationship with the media one-to-many.
     * 
     * @var Productmedia Media of Product
     * @ORM\OneToMany(targetEntity="ProductMedia", mappedBy="product", cascade={"persist", "remove", "merge", "refresh"}, orphanRemoval=true)
     */
    private $media;

    /**
     * It represents the relationship with the media many-to-many.
     * 
     * @var ProductCategory Category of Products
     * @ORM\ManyToMany(targetEntity="ProductCategory", mappedBy="products", cascade={"persist","refresh"})
     */
    private $categories; //represent owners object

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * It takes on the form.
     * 
     * @param string $name Name of Product
     * @return string This name
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * Method initializations Array Colections. 
     */
    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
     * Get media of product
     * 
     * @return Collection All media
     */
    function getMedia()
    {
        return $this->media;
    }

    /**
     * Set Media for this Product
     * 
     * @param ProductMedia $media
     * @return $this
     */
    function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Get slug.
     * @return string Slug value for this Product entity
     */
    function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set Product slug
     * 
     * @param string $slug Slug value for this Product
     * @return \Sulmi\ProductBundle\Entity\Product
     */
    function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get Categories
     * 
     * @return Collection Categories related on this Product
     */
    function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set collection of Categories
     * 
     * @param ArrayCollection $categories
     * @return $this
     */
    function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Magic method needs in form.
     * 
     * @return string This Product name
     */
    function __toString()
    {
        return $this->name;
    }

    /**
     * Get Description
     * 
     * @return string Description product.
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     * 
     * @param string $description Description text of this product
     * @return $this
     */
    function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get Locale
     * 
     * @return string Actual locale on product version language
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set Locale
     * 
     * @param string $locale Actual Locale field value
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get Price
     * 
     * @return decimal Price of this product
     */
    function getPrice()
    {
        return $this->price;
    }

    /**
     * Set Price this product
     * 
     * @param decimal $price
     * @return $this
     */
    function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

}