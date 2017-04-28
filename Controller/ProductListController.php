<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Entity\ProductCategory;
use Sulmi\ProductBundle\Entity\ProductMedia;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller supports basic product for upload single or multiple files.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 *
 * @Route("")
 */
class ProductListController extends Controller
{

    /**
     * @var Uploadmanager 
     */
    private $uploadableManager;

    /**
     * Viewing category and its products.
     * 
     * @param Request $request
     * @param string $categoryslug Slug of Category not used
     * @param ProductCategory $category Id of Category
     * @return Response Symfony Action Response
     *
     * @Route("/{categoryslug}/c/{id}", name="category_default_list", requirements={"id" = "\d+","categoryslug" = "[\w\-_]+"})
     * @Method({"GET","POST"})
     */
    public function indexAction(Request $request, $categoryslug, ProductCategory $category)
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
            return $this->render('AppBundle:HomePage:homePage.html.twig', [
                        'products' => $productpagination,
                        'category' => $category,
            ]);
        }
    }

    /**
     * Lists all productCategory entities.
     *
     * @Route("/new/product/in/categorry/{id}", name="new_product_for_category", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function newProductForCategoryAction(Request $request, ProductCategory $productCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $productCategories = $product->getCategories();
        $productCategories->add($productCategory);
        $product->setCategories($productCategories);

        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $categoryProducts = $productCategory->getProducts();
            $categoryProducts->add($product);
            $productCategory->setProducts($categoryProducts);
            $em->persist($productCategory);
            $em->flush();

            return $this->redirectToRoute('category_default_language', [
                        'id' => $productCategory->getId(),
                        'categoryslug' => $productCategory->getSlug()
            ]);
        }
        return $this->render('SulmiProductBundle:ProductCategory:index_products_new_product.html.twig', [
                    'category' => $productCategory,
                    'id' => $productCategory->getId(),
                    'product' => $product,
                    'edit_form' => $form->createView(),
        ]);
    }

    /**
     * You can save files sent using an extension Doctrine.
     * 
     * @param ProductMedia $product Media media from form
     * @param File $picture Temp name and other data uploaded file
     * @param Product $product Product entity
     * @param EntityManager $em Doctrine Entity Manager
     */
    private function markToUploadNewProductMedia($productMedia, $picture, $product, $em)
    {
        $productMedia->setPicture($picture);
        $productMedia->setProduct($product);
        $em->persist($productMedia);
        $this->uploadableManager->markEntityToUpload($productMedia, $productMedia->getPicture());
    }

    /**
     * You can save files sent from form of multiple choice.
     * 
     * @param Form $form
     * @param ProductMedia $productMedia
     */
    private function persistMultipleFile($form, $productMedia)
    {
        $pictures = $form->getData()->getPicture();
        $picturesCount = count($pictures);
        $em = $this->getDoctrine()->getManager();
        if ($picturesCount > 0) {
            $product = $productMedia->getProduct();
            $picture = array_slice($pictures, 0, 1);
            $this->markToUploadProductMedia($productMedia, $picture[0], $em);
            $clicedPictures = array_slice($pictures, 1);
            if (count($clicedPictures) > 0) {
                foreach ($clicedPictures as $picture) {
                    $productMedia = new ProductMedia();
                    $this->markToUploadNewProductMedia($productMedia, $picture, $product, $em);
                }
            }
        }
        $em->flush();
    }

}