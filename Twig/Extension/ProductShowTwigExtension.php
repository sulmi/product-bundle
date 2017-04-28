<?php

namespace Sulmi\ProductBundle\Twig\Extension;

use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Helpers\TwigMediaTemplateConfigurator;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Product Twig Extension
 *
 * @author    Miroslaw Sulowski <miroslaw-sulowski@o2.pl>
 */
class ProductShowTwigExtension extends Twig_Extension
{

    private $template;

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('product_has_media', [$this, 'productHasMedia'], []),
            new Twig_SimpleFunction('render_product_media90', [$this, 'renderProduct'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
                    ]),
            new Twig_SimpleFunction('render_product_media800', [$this, 'renderProduct800'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
                    ]),
        ];
    }

    public function renderProduct(Twig_Environment $env, Product $product)
    {
        $firstMedia = $this->getFirstMedia($product);
        $mime = 'no-media';
        if ($firstMedia !== null) {
            $mime = $firstMedia->getMime();
        }

        $this->template = $this->getTemplate($mime);

        return $env->render($this->template, [
                    'media' => $firstMedia,
                    'media_size' => null,
        ]);
    }

    public function renderProduct800(Twig_Environment $env, Product $product, $media_size = null)
    {
        $firstMedia = $this->getFirstMedia($product);
        $mime = 'no-media';
        if ($firstMedia !== null) {
            $mime = $firstMedia->getMime();
        }

        $this->template = $this->getTemplate($mime);

        return $env->render($this->template, [
                    'media' => $firstMedia,
                    'media_size' => $media_size,
        ]);
    }

    public function productHasMedia(Product $product)
    {
        $firstMedia = $this->getFirstMedia($product);
        if ($firstMedia !== null) {
            return true;
        }
        return false;
    }

    public function getName()
    {
        return 'render_product';
    }

    public function getTemplate($mediaMime)
    {
        return TwigMediaTemplateConfigurator::recognizeTemplate($mediaMime);
    }

    public function getFirstMedia($product)
    {
        $images = $product->getMedia()->getValues();

        if (count($images) > 0) {
            $image = array_slice($images, 0, 1);
            return $image[0];
        }
        return null;
    }

}