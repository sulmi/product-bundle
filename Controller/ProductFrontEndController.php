<?php

namespace Sulmi\ProductBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sulmi\ProductBundle\Controller\ProductBaseController as BaseController;
use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Entity\ProductCategory;
use Sulmi\ProductBundle\Entity\ProductMedia;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for the proper display of products, 
 * and some of the functionality associated with the product and ajax actions.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @uses Sulmi\ProductBundle\Controller\ProductBaseController as BaseController
 */
class ProductFrontEndController extends BaseController
{

    /**
     * Home page default Action with select language
     *
     * @Route("/", name="sulmi_product_homepage_lang")
     * @Method({"GET","POST"})
     * 
     * @param Request $request
     * @return Response
     */
    public function homePageLangAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $productsQuery = $em->getRepository('SulmiProductBundle:Product')->findListAllProducts();
        $productpagination = $paginator->paginate($productsQuery, $request->query->getInt('page', 1), 18);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SulmiProductBundle::partial/product/product_paginated_ajax.html.twig', [
                        'products' => $productpagination,
            ]);
        } else {
            return $this->render('SulmiProductBundle:HomePageDefault:homePage.html.twig', [
                        'products' => $productpagination,
            ]);
        }
    }

    /**
     * Lists all productCategory entities with language for celect category.
     * 
     * @param Request $request
     * @param string $categoryslug It's not used
     * @param ProductCategory $category Category Products
     * @return Response Symfony Action Response
     *
     * @Route("/{categoryslug}/c/{id}", name="category_default_language", requirements={"id" = "\d+","categoryslug" = "[\w\-_]+"})
     * @Method({"GET","POST"})
     */
    public function categoryDefaultLanguageAction(Request $request, $categoryslug, ProductCategory $category)
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
     * Viewing the product with language version.
     * 
     * @param Product $product
     * @param type $slug
     * @return Response Symfony Action Response
     * 
     * @Route("/{slug}/{id}", name="product_default_language", requirements={"id" = "\d+","slug" = "[\w\-_]+"})
     * @Method({"GET","POST"})
     */
    public function languageProductShowAction(Product $product, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductMedia');
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

    /**
     * Finds and displays a productMedia entity.
     * 
     * @param ProductMedia $productMedia
     * @return Response Symfony Action Response
     *
     * @Route("/public/media/{id}", name="sulmi_product_productmedia_show", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function showMediaAction(ProductMedia $productMedia)
    {
//        $deleteForm = $this->createDeleteForm($productMedia);
        $product = $productMedia->getProduct();
        if (is_object($product)) {
            $productCategoryPersistentCollection = $product->getCategories();
            $productCategories = $productCategoryPersistentCollection->getValues();
        } else {
            $product = new Product();
            $product->setName('noname');
            $productCategories[] = new ProductCategory();
            $productCategories[0]->setTitle('notitle');
        }
        return $this->render('SulmiProductBundle:ProductMedia:show.html.twig', array(
                    'productMedia' => $productMedia,
                    'product' => $product,
                    'productCategories' => $productCategories,
//                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a product entity.
     * 
     * @param Product $product
     * @return Response Symfony Action Response
     *
     * @Route("/product/{id}", name="sulmi_product_product_show", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
//        $deleteForm = $this->createDeleteForm($product);
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductMedia');
        $images = $repo->findAllImages($product->getId());
        $medias = $repo->findAllDocuments($product->getId());
        $videos = $repo->findAllMovies($product->getId());
        if (count($videos) > 0) {
            $videos = $this->getVideoThumbnails($em, $videos);
        }

        return $this->render('SulmiProductBundle:Product:show.html.twig', [
                    'images' => $images,
                    'medias' => $medias,
                    'videos' => $videos,
                    'categories' => $product->getCategories()->getValues(),
                    'product' => $product,
//                    'delete_form' => $deleteForm->createView(),
        ]);
    }

}