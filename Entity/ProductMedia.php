<?php

namespace Sulmi\ProductBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Sulmi\ProductBundle\Repository\ProductMediaRepository;

/**
 * ProductMedia using Doctrine Extensions
 * 
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 *
 * @ORM\Table(name="product_media")
 * @ORM\Entity(repositoryClass="Sulmi\ProductBundle\Repository\ProductMediaRepository")
 * @Gedmo\Uploadable(appendNumber=true, pathMethod="getPath", filenameGenerator="ALPHANUMERIC")
 * @ORM\HasLifecycleCallbacks()
 * filenameGenerator="ALPHANUMERIC", callback="callbackMethod", filenameGenerator="SHA1"
 */
class ProductMedia {

    /**
     * Unique id for this entity
     * 
     * @var int $id unique id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Path for uploaded file
     *
     * @var string
     *  
     * @ORM\Column(name="path", type="string", nullable=true)
     * @Gedmo\UploadableFilePath
     */
    private $filePath;

    /**
     * Size of file in bytes
     *
     * @var decimal
     *  
     * @ORM\Column(name="size", type="decimal", nullable=true)
     * @Gedmo\UploadableFileSize
     */
    private $size;

    /**
     * Determine if has thumbnails
     *
     * @var bool if has thumbnails
     * 
     * @ORM\Column(name="thumbs", type="boolean")
     */
    private $thumbs = false;

    /**
     * Mime file
     *
     * @var string Mime for media
     * 
     * @ORM\Column(name="mime_type", type="string", nullable=true)
     * @Gedmo\UploadableFileMimeType
     */
    private $mime;

    /**
     * Related Product
     *
     * @var Product Product entity 
     * 
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="media", cascade={"persist","refresh","merge"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     *
     * @var File represents file object 
     */
    private $picture;

    /**
     * @return id Identity I
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set file path
     * 
     * @param string $filePath file path for uploaded
     */
    public function setFilePath($filePath) {
        $this->filePath = $filePath;
    }

    /**
     * Get file path
     * 
     * @return string File path for file 
     */
    public function getFilePath() {
        return $this->filePath;
    }

    /**
     * Get path for upload file
     * 
     * @param string $basePath
     * @return string base path
     */
    public function getPath($basePath = null) {

        return 'upload';
    }

    /**
     * Set mime type for uploaded file
     * 
     * @param string $mime
     */
    public function setMime($mime) {
        $this->mime = $mime;
    }

    /**
     * Get mime for file
     * 
     * @return string Ful mime type for file
     */
    public function getMime() {
        return $this->mime;
    }

    /**
     * Set size
     * 
     * @param decimal $size Size for file in bytes
     */
    public function setSize($size) {
        $this->size = $size;
    }

    /**
     * Get size file
     * 
     * @return decimal Getting size of file
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Get picture as file
     * 
     * @return object File represent
     */
    function getPicture() {
        return $this->picture;
    }

    /**
     * Set poicture as file
     * 
     * @param File $picture uploaded file
     * @return $this
     */
    function setPicture($picture) {
        $this->picture = $picture;
        return $this;
    }

    /**
     * Get Product entity
     * 
     * @return Product object entity
     */
    function getProduct() {
        return $this->product;
    }

    /**
     * Set product
     * 
     * @param Product $product Product entity
     * @return $this
     */
    function setProduct($product) {
        $this->product = $product;
        return $this;
    }

    /**
     * Magic needs in form
     * 
     * @return string File path
     */
    function __toString() {
        return $this->filePath;
    }

    /**
     * Get thumbnails bool value
     * 
     * @return bool determines if has thumbnails
     */
    function getThumbs() {
        return $this->thumbs;
    }

    /**
     * Set thumbnails if is movie
     * 
     * @param bool $thumbs if has thumbnails files
     * @return $this
     */
    function setThumbs($thumbs) {
        $this->thumbs = $thumbs;
        return $this;
    }

}
