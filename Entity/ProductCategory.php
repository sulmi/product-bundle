<?php

namespace Sulmi\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Entity\ProductCategoryTranslation;

/**
 * ProductCategory
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @ORM\Table(name="product_category")
 * @ORM\Entity(repositoryClass="Sulmi\ProductBundle\Repository\ProductCategoryRepository")
 * @Gedmo\Tree(type="nested")
 * @Gedmo\TranslationEntity(class="Sulmi\ProductBundle\Entity\ProductCategoryTranslation")
 * @ORM\HasLifecycleCallbacks()
 */
class ProductCategory
{

    /**
     * Unique id firld
     * 
     * @var integer 
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $id;

    /**
     * Title for this categroy
     * 
     * @var string Title this category 
     * 
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=128)
     * @Assert\NotBlank()
     * @Assert\Length(min = 3, max = 128)
     */
    private $title;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="ProductCategory", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="ProductCategory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * Simplify name
     * 
     * @var string Slug for this category
     * 
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * The name of the router for static pages
     * 
     * @var string Slug for this category
     * 
     * @ORM\Column(length=128, unique=false, nullable=true)
     */
    private $routename;

    /**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="categories", cascade={"persist","refresh"})
     * @ORM\JoinTable(name="categories_products", joinColumns={
     * @ORM\JoinColumn(name="cat_id",referencedColumnName="id", unique=false, nullable=true)},inverseJoinColumns={
     * @ORM\JoinColumn(name="prod_id",referencedColumnName="id", unique=false, nullable=true)}
     * )
     */
    private $products;

    /**
     * @ORM\OneToMany(
     *   targetEntity="ProductCategoryTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * Initialise collections
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->children = new ArrayCollection();
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
     * Get slug
     * @return string Slug value
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set Title Category
     * 
     * @param type $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     *  Get Title category
     * @return type
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set parent
     * 
     * @param $this $parent
     */
    public function setParent(ProductCategory $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get Parent
     * 
     * @return type
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get Left Position
     * 
     * @return type
     */
    function getLft()
    {
        return $this->lft;
    }

    /**
     * Get Right Position
     * 
     * @return type
     */
    function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Get Level
     * 
     * @return int
     */
    function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Get Childrens Collection
     * 
     * @return ArrayCollection Childrens Collection
     */
    function getChildren()
    {
        return $this->children;
    }

    /**
     * Get all related products ArrayCollection
     * 
     * @return ArrayCollection Products Collection
     */
    function getProducts()
    {
        return $this->products;
    }

    /**
     * Set Left position
     * 
     * @param int $lft
     * @return $this
     */
    function setLft($lft)
    {
        $this->lft = $lft;
        return $this;
    }

    /**
     * Set right position
     * 
     * @param int $rgt
     * @return $this
     */
    function setRgt($rgt)
    {
        $this->rgt = $rgt;
        return $this;
    }

    /**
     * Set Left position
     * 
     * @param int $lvl
     * @return $this
     */
    function setLvl($lvl)
    {
        $this->lvl = $lvl;
        return $this;
    }

    /**
     * Set childrens collection
     * 
     * @param ArrayCollection $children All childrens Collection
     * @return $this
     */
    function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Set products collection
     * 
     * @param type $products
     * @return $this
     */
    function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    /**
     * Magic is needed in form, return this title
     * 
     * @return type
     */
    function __toString()
    {
        return $this->title;
    }

    /**
     * It takes on the form.
     * 
     * @param string $name Name of ProductCategory
     * @return string This name
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * Get root marker
     * 
     * @return int If this is root
     */
    function getRoot()
    {
        return $this->root;
    }

    /**
     * Set root marker
     * 
     * @param bool $root
     * @return $this
     */
    function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * Get route string.
     * When create navigation this automatic value.
     * 
     * @return string The route if static page is loaded
     */
    function getRoutename()
    {
        return $this->routename;
    }

    /**
     * Set roiuyte name string if needed
     * 
     * @param string $routename
     * @return $this
     */
    function setRoutename($routename)
    {
        $this->routename = $routename;
        return $this;
    }

    /**
     * Translations for this category.
     * 
     * @return ArrayCollection provides all translations
     */
    function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Collection of translations
     * 
     * @param ArrayCollection $translations
     * @return $this
     */
    function setTranslations($translations)
    {
        $this->translations = $translations;
        return $this;
    }

    /**
     * Set slug as minified title
     * 
     * @param type $slug
     * @return $this
     */
    function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Set iid for fresh category
     * 
     * @param int $id
     * @return $this
     */
    function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Locale for this entity
     * 
     * @param string $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

}