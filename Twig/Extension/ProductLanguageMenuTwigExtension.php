<?php

namespace Sulmi\ProductBundle\Twig\Extension;

use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Product Twig Extension
 *
 * @author    Miroslaw Sulowski <miroslaw-sulowski@o2.pl>
 * @copyright Miroslaw Sulowski
 */
class ProductLanguageMenuTwigExtension extends Twig_Extension
{

    private $requestStack;
    private $container;
    private $currentRequest;
    private $currentRouteName;
    private $defaultLocale;
    private $requestUri;
    private $locale;
    private $baseUrl;
    private $languages;

    public function __construct(ContainerInterface $container, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('render_language_menu', [$this, 'renderMenu'], [
                'is_safe' => ['html'],
                'needs_environment' => true // Tell twig we need the environment
                    ]),
        ];
    }

    public function renderMenu(Twig_Environment $env, $options = null)
    {

//        $em = $this->container->get('doctrine.orm.entity_manager');
        
        $this->currentRequest = $this->requestStack->getCurrentRequest();

        $this->currentRouteName = $this->currentRequest->attributes->all()['_route'];
        $this->defaultLocale = $this->currentRequest->getDefaultLocale();
        $this->requestUri = $this->currentRequest->getRequestUri();
        $this->locale = $this->currentRequest->getLocale();
        $this->baseUrl = $this->currentRequest->getBaseUrl();

        $this->joinLocales();

        switch ($this->currentRouteName) {
            case 'sulmi_product_homepage':
                $this->prepareLanguagesHome();
                break;
            case 'sulmi_product_homepage_lang':
                $this->prepareLanguagesHome();
                break;
            default:
                $this->prepareLanguagesHome();
                break;
        }

        return $env->render('@SulmiProductBundle/Product/menu.html.twig', [
                    'languages' => $this->languages
        ]);
    }

    public function getName()
    {
        return 'sulmi_product_render_menu_languages';
    }

    public function prepareLanguagesHome()
    {
        $l = -1;
        foreach ($this->languages as $menuRow) {
            $menuRow->url = $this->menuHomeRowReplace($menuRow);
            $this->languages[++$l] = $menuRow;
        }
    }

    public function joinLocales()
    {
        $languagesArraytransKeys = explode('|', $this->container->getParameter('app_locales_transKeys'));
        $languagesArray = explode('|', $this->container->getParameter('app_locales'));

        $l = -1;

        foreach ($languagesArray as $language) {
            $menuRow = new stdClass();
            $menuRow->language = $language;

            if ($this->locale == $menuRow->language) {
                $menuRow->classActive = true;
            } else {
                $menuRow->classActive = false;
            }

            $menuRow->url = preg_replace(['/\/' . $this->locale . '\//', '/\/\//',], ['/' . $language . '/', '/',], $this->requestUri, 1);

            if ($this->baseUrl . '/' == $this->requestUri && $this->defaultLocale !== $language && $l > -1) {
                $menuRow->url .= $language . '/';
            }

            $menuRow->transKey = $languagesArraytransKeys[++$l];
            $this->languages[] = $menuRow;
        }
    }

    function menuHomeRowReplace($menuRow)
    {
        if (strlen($this->baseUrl) > 3) {
            $locale = '\/' . $menuRow->language . '\/';
        } else {
            $locale = '\/';
        }

        $paterns = [
            '/' . $locale . '/' => '/',
            '/app_dev\.php\//' => 'app_dev.php/' . $menuRow->language . '/',
            '/app_dev\.php\//' => 'app_dev.php/' . $menuRow->language . '/',
            '/app\.php\//' => 'app.php/' . $menuRow->language . '/',
        ];
        return preg_replace(array_keys($paterns), array_values($paterns), $menuRow->url, 1);
    }

}