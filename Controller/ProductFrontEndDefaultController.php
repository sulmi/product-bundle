<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Entity\ProductCategory;
use Sulmi\ProductBundle\Controller\ProductBaseController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible for the proper display of products, 
 * and some of the functionality associated with the product and ajax actions.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @uses Sulmi\ProductBundle\Controller\ProductBaseController as BaseController
 * 
 * Route("/productajax")
 */
class ProductFrontEndDefaultController extends BaseController
{

    /**
     * Home page default Action without select language
     *
     * @Route("/", name="sulmi_product_homepage")
     * @Method({"GET","POST"})
     * 
     * @param Request $request
     * @return Response
     */
    public function homePageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $productsQuery = $em->getRepository('SulmiProductBundle:Product')->findListAllProducts();
        $productpagination = $paginator->paginate($productsQuery, $request->query->getInt('page', 1), 12);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SulmiProductBundle::partial/product/product_paginated_ajax.html.twig', [
                        'products' => $productpagination,
            ]);
        }

        return $this->render('SulmiProductBundle:HomePageDefault:homePage.html.twig', [
                    'products' => $productpagination,
        ]);
    }

    /**
     * Lists all Product Category entities without language for celect category.
     * 
     * @param Request $request
     * @param string $categoryslug
     * @param ProductCategory $category
     * @return Response Symfony Action Response
     *
     * @Route("/{categoryslug}/c/{id}", name="category_default", requirements={"id" = "\d+","categoryslug" = "[\w\-_]+"})
     * @Method({"GET","POST"})
     */
    public function categoryDefaultAction(Request $request, $categoryslug, ProductCategory $category)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $productpagination = $paginator->paginate($category->getProducts()->getValues(), $request->query->getInt('page', 1), 12);
        if ($request->isXmlHttpRequest()) {
            return $this->render('SulmiProductBundle::partial/product/product_paginated_ajax.html.twig', [
                        'products' => $productpagination,
                        'category' => $category,
            ]);
        } else {
            return $this->render('SulmiProductBundle:ProductCategory:index_products.html.twig', [
                        'products' => $productpagination,
                        'category' => $category,
                        'id' => $category->getId(),
            ]);
        }
    }

    /**
     * Viewing the product without language version.
     * 
     * @param Product $product
     * @param type $slug
     * @return Response Symfony Action Response
     *
     * @Route("/{slug}/{id}", name="product_default", requirements={"id" = "\d+","slug" = "[\w\-_]+"})
     * @Method({"GET","POST"})
     */
    public function defaultProductShowAction(Product $product, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductMedia');
        $repoProduct = $em->getRepository('SulmiProductBundle:ProductMedia');
        $images = $repo->findAllImages($product->getId());
        $medias = $repo->findAllDocuments($product->getId());
        $videos = $repo->findAllMovies($product->getId());
        if (count($videos) > 0) {
            $videos = $this->getVideoThumbnails($em, $videos);
        }
        return $this->render('SulmiProductBundle:ProductDefault:show.html.twig', [
                    'images' => $images,
                    'medias' => $medias,
                    'videos' => $videos,
                    'categories' => $product->getCategories()->getValues(),
                    'product' => $product,
        ]);
    }

}