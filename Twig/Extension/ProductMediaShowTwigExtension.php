<?php

namespace Sulmi\ProductBundle\Twig\Extension;

use Sulmi\ProductBundle\Entity\ProductMedia;
use Sulmi\ProductBundle\Helpers\TwigMediaTemplateConfigurator;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Product Twig Extension
 *
 * @author    Miroslaw Sulowski <miroslaw-sulowski@o2.pl>
 * @copyright Miroslaw Sulowski
 */
class ProductMediaShowTwigExtension extends Twig_Extension
{

    private $template;

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('render_media90', [$this, 'renderMedia'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
                    ]),
            new Twig_SimpleFunction('render_media800', [$this, 'renderMedia800'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
                    ]),
        ];
    }

    public function renderMedia(Twig_Environment $env, ProductMedia $productMedia)
    {

        if (!is_object($productMedia)) {
            $this->template = '@SulmiProductBundle/partial/product_no_media.html.twig';

            return $env->render($this->template, [
                        'media' => $productMedia,
                        'media_size' => null,
            ]);
        }
        $this->template = $this->getTemplate($productMedia->getMime());

        return $env->render($this->template, [
                    'media' => $productMedia,
                    'media_size' => null,
        ]);
    }

    public function renderMedia800(Twig_Environment $env, ProductMedia $productMedia, $media_size = null)
    {

        if (!is_object($productMedia)) {
            $this->template = '@SulmiProductBundle/partial/product_no_media.html.twig';

            return $env->render($this->template, [
                        'media' => $productMedia,
                        'media_size' => '800',
            ]);
        }
        $this->template = $this->getTemplate($productMedia->getMime());

        return $env->render($this->template, [
                    'media' => $productMedia,
                    'media_size' => '800',
        ]);
    }

    public function getName()
    {
        return 'render_media';
    }

    public function getTemplate($mediaMime)
    {
        return TwigMediaTemplateConfigurator::recognizeTemplate($mediaMime);
    }

}