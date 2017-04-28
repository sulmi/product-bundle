<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Entity\ProductCategory;
use Sulmi\ProductBundle\Entity\ProductMedia;
use Sulmi\ProductBundle\Controller\ProductBaseController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible for the proper display of products, 
 * and some of the functionality associated with the product and ajax actions.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @uses Sulmi\ProductBundle\Controller\ProductBaseController as BaseController
 * 
 * @Route("/productajax")
 */
class ProductAjaxController extends BaseController
{

    /**
     * Displays all products divided into pages using AJAX.
     *
     * @Route("/", name="product_index_ajax")
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $productsQuery = $em->getRepository('SulmiProductBundle:Product')->findListAllProducts();
        $productpagination = $paginator->paginate($productsQuery, $request->query->getInt('page', 1), 12);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SulmiProductBundle::partial/product/product_paginated_scroll_ajax.html.twig', [
                        'products' => $productpagination,
            ]);
        } else {
            return $this->render('SulmiProductBundle:HomePageDefault:homePage.html.twig', [
                        'products' => $productpagination,
            ]);
        }
    }

    /**
     * Provides for page categories all products for given category.
     *
     * @Route("/smallproducts/{id}", name="product_index_small_ajax", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param ProductCategory $productCategory Category of product
     * @return Response
     */
    public function indexSmallAction(ProductCategory $productCategory)
    {

        $em = $this->getDoctrine()->getManager();
        $products = $productCategory->getProducts();
        return $this->render('SulmiProductBundle::partial/product_index_small_ajax.html.twig', [
                    'products' => $this->sortCollection($products, 'id', 'desc'),
        ]);
    }

    /**
     * Provides for page categories all products.
     *
     * @Route("/getproducts/{id}", name="getproducts_ajax", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * 
     * 
     */
//    public function getProductsAction(ProductCategory $productCategory)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $products = $productCategory->getProducts()->toArray();
//        return $this->render('SulmiProductBundle::partial/product_index_ajax.html.twig', [
//                    'products' => $products,
//        ]);
//    }
    /**
     * Simple collection sort part provides filter callback
     * 
     * @param type $array
     * @return type
     */
    public function doFilter($array)
    {
        return array_filter($array, array($this, 'callbackMethodName'));
    }

    /**
     * Simple collection sort callback
     * 
     * @param type $element
     * @return type
     */
    protected function callbackMethodName($element)
    {
        return $element % 2 === 0;
    }

    /**
     * Mechanism for adding product categories.
     * If you add a product in response you get a list of products for the category.
     * 
     * @Route("/addproduct/{id}/{productid}", name="getproducts_ajax", requirements={"id" = "\d+","productid" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param ProductCategory $productCategory Category for product
     * @param type $productid Poduct to join category
     * @return Response
     */
    public function AddProductAction(ProductCategory $productCategory, $productid)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('SulmiProductBundle:Product')->find(['id' => $productid]);
        $products = $productCategory->getProducts();

        if ($products->contains($product)) {
            return $this->render('SulmiProductBundle::partial/product_index_small_ajax.html.twig', [
                        'products' => $this->sortCollection($products, 'id', 'desc'),
            ]);
        }

        $products->add($product);
        $productCategory->setProducts($products);
        $em->persist($productCategory);
        $em->flush();

        $products = $productCategory->getProducts();

        return $this->render('SulmiProductBundle::partial/product_index_small_ajax.html.twig', [
                    'products' => $this->sortCollection($products, 'id', 'desc'),
        ]);
    }

    /**
     * Mechanism for adding product categories.
     * If you add a product in response you get a list of products for the category.
     * 
     * @Route("/addincategory/{id}/{productid}", name="sulmi_product_addincategory_ajax", requirements={"id" = "\d+","productid" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param ProductCategory $productCategory Category for product
     * @param type $productid Poduct to join category
     * @return Response
     */
    public function AddInCategoryAction(ProductCategory $productCategory, $productid)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('SulmiProductBundle:Product')->find(['id' => $productid]);
        $products = $productCategory->getProducts();

        if ($products->contains($product)) {
            return $this->render('SulmiProductBundle::partial/product_index_small_ajax.html.twig', [
                        'products' => $this->sortCollection($products, 'id', 'desc'),
            ]);
        }

        $products->add($product);
        $productCategory->setProducts($products);
        $em->persist($productCategory);
        $em->flush();

        $products = $productCategory->getProducts();

        return $this->render('SulmiProductBundle::partial/product_index_small_ajax.html.twig', [
                    'products' => $this->sortCollection($products, 'id', 'desc'),
        ]);
    }

    /**
     * Removing the product from the collection category.
     * Using the action will receive updated list of products for select category.
     *
     * @Route("/removeproduct/{id}/{productid}", name="removeproduct_ajax", requirements={"id" = "\d+","productid" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param ProductCategory $productCategory
     * @param integer $productid
     * @return Response
     */
    public function RemoveProductAction(ProductCategory $productCategory, $productid)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('SulmiProductBundle:Product')->find(['id' => $productid]);
        $products = $productCategory->getProducts();

        if ($products->contains($product)) {
            $index = $products->indexOf($product);
            $products->remove($index);
            $productCategory->setProducts($products);
            $em->persist($productCategory);
            $em->flush();
            return $this->render('SulmiProductBundle::partial/product_index_small_ajax.html.twig', [
                        'products' => $products,
            ]);
        }

        $products = $productCategory->getProducts();

        return $this->render('SulmiProductBundle::partial/product_index_small_ajax.html.twig', [
                    'products' => $this->sortCollection($products, 'id', 'desc'),
        ]);
    }

    /**
     * Provides form for uploads files.
     *
     * @Route("/productaddmediaajaxform/{id}", name="product_ajax_form", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function ajaxNewMediaFormShowAction(Request $request, Product $product)
    {

        $entity = new ProductMedia();
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductMediaType', $entity);
        $response = $this->render('SulmiProductBundle:Product:form.html.twig', array(
            'id' => $product->getId(),
            'form' => $form->createView(),
        ));
        return $response;
    }

    /**
     * Saving uploading files.
     *
     * @Route("/productaddmedia/{id}", name="product_add_new_media", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    public function ajaxAddNewMediaAction(Request $request, Product $product)
    {
        $entity = new ProductMedia();
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductMediaType', $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->render('SulmiProductBundle:Product:form.html.twig', ['message' => 'Success!'], 200);
        }

        $response = new JsonResponse(
                [
            'message' => 'Error',
            'form' => $this->renderView('SulmiProductBundle:Product:form.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView(),
            ))], 400);

        return $response;
    }

    /**
     * Sends form for new product under specific category and create new product record.
     *
     * @Route("/newincategory/{id}", name="product_new_in_category_ajax", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * @param ProductCategory $productCategory
     * @return Response Form for new product or list produucts
     */
    public function newInCategoryAction(Request $request, ProductCategory $productCategory)
    {
        $product = new Product();
        $productCategories = $product->getCategories();
        $productCategories->add($productCategory);

        $productCategoryProducts = $productCategory->getProducts();
        $productCategoryProducts->add($product);

        $product->setCategories($productCategories);
        $productCategory->setProducts($productCategoryProducts);

        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductQuickAjaxType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->render('SulmiProductBundle::partial/product_newincategory_ajax_form.html.twig', [
                        'form' => $form->createView(),
                        'products' => $this->sortCollection($productCategoryProducts, 'id', 'desc'),
            ]);
        }

        return $this->render('SulmiProductBundle::partial/product_newincategory_ajax_form.html.twig', [
                    'form' => $form->createView(),
                    'products' => null,
        ]);
    }

    /**
     * List of all images for the select product.
     *
     * @Route("/allimages/{id}", name="product_all_images", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     * 
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function getAllImagesAction(Request $request, Product $product)
    {
        $em = $this->getDoctrine()->getManager();
        $images = $em->getRepository('SulmiProductBundle:ProductMedia')
                ->findAllImages($product->getId());

        return $this->render('SulmiProductBundle:Product:allimages.html.twig', array(
                    'images' => $images,
        ));
    }

    /**
     * Finds and displays a product entity.
     *
     * @param Product $product
     * @return Response Wiew product page and admin widgets.
     * 
     * @Route("/{id}", name="sulmi_product_product_show", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
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
                    'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing product entity.
     * 
     * @param Request $request
     * @param Product $product
     * @return Response Form for edit product entity
     *
     * @Route("/{id}/edit", name="product_edit", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $categories = $product->getCategories();
        $em = $this->getDoctrine()->getManager();
        $images = $em->getRepository('SulmiProductBundle:ProductMedia')
                ->findAllImages($product->getId());
        $repoCategories = $editForm = $this->createForm('Sulmi\ProductBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_edit', array('id' => $product->getId()));
        }

        return $this->render('SulmiProductBundle:Product:edit.html.twig', array(
                    'product' => $product,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     * 
     * @param Request $request
     * @param Product $product
     * @return Response
     *
     * @Route("/{id}", name="product_delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush($product);
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     * @return Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}