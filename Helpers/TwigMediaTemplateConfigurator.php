<?php

namespace Sulmi\ProductBundle\Helpers;

/**
 * Assigns appropriate template depending on file type.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class TwigMediaTemplateConfigurator
{

    /**
     * Switch templates.
     * 
     * @param string $mediaMime
     * @return string Template for file
     */
    public static function recognizeTemplate($mediaMime)
    {
        switch ($mediaMime) {
            case 'image/jpeg':
                $template = 'SulmiProductBundle::partial/media/product_media_imagejpeg.html.twig';
                break;
            case 'image/png':
                $template = 'SulmiProductBundle::partial/media/product_media_imagejpeg.html.twig';
                break;
            case 'image/gif':
                $template = 'SulmiProductBundle::partial/media/product_media_imagejpeg.html.twig';
                break;
            case 'application/pdf':
                $template = 'SulmiProductBundle::partial/media/product_media_applicationpdf.html.twig';
                break;
            case 'text/plain':
                $template = 'SulmiProductBundle::partial/media/product_media_textplain.html.twig';
                break;
            case 'video/mp4':
                $template = 'SulmiProductBundle::partial/media/product_media_videomp4.html.twig';
                break;
            case 'video/x-flv':
                $template = 'SulmiProductBundle::partial/media/product_media_videox-flv.html.twig';
                break;

            default:
                $template = 'SulmiProductBundle::partial/product_no_media.html.twig';
                break;
        }
        return $template;
    }

}