<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Controller\ProductBaseController as BaseController;
use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Entity\ProductCategory;
use Sulmi\ProductBundle\Entity\ProductMedia;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Product controller.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 *
 * @Route("/product/admin")
 */
class ProductController extends BaseController {

    /**
     * Lists all product entities.
     *
     * @param Request $request
     * @return Response Symfony Action Response
     *
     * @Route("/", name="sulmi_product_index")
     * @Method({"GET","POST"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $productsQuery = $em->getRepository('SulmiProductBundle:Product')->findListAllProducts();
        $productpagination = $paginator->paginate($productsQuery, $request->query->getInt('page', 1), 12);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SulmiProductBundle::partial/product/product_paginated_ajax.html.twig', [
                'products' => $productpagination,
            ]);
        } else {
            return $this->render('SulmiProductBundle:Product:index.html.twig', [
                'products' => $productpagination,
            ]);
        }
    }

    /**
     * Creates a new product entity form.
     *
     * @param Request $request Symfony Request
     * @param Product $product Product id
     * @return Response Symfony Action Response
     *
     * @Route("/productaddmediaajaxform/{id}", name="product_ajax_form", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function ajaxNewMediaFormShowAction(Request $request, Product $product) {
        $entity = new ProductMedia();
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductMediaType', $entity);
        $response = $this->render('SulmiProductBundle:Product:form.html.twig', array(
            'id' => $product->getId(),
            'form' => $form->createView(),
        ));

        return $response;
    }

    /**
     * Adds new media for the selected product.
     *
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     *
     * @Route("/productaddmedia/{id}", name="product_add_new_media", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function ajaxAddNewMediaAction(Request $request, Product $product) {
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
     * Creates a new product entity.
     *
     * @param Request $request
     * @return Response Symfony Action Response
     *
     * @Route("/new", name="sulmi_product_product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $product = new Product();
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush($product);
            return $this->render('SulmiProductBundle:Product:new.html.twig', array(
                'product' => $product,
                'form' => $form->createView(),
                'images' => [],
                'videos' => [],
                'medias' => [],
            ));
        }
        return $this->render('SulmiProductBundle:Product:new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
            'images' => [],
            'videos' => [],
            'medias' => [],
        ));
    }

    /**
     * New product for the selected category.
     *
     * @param Request $request
     * @param ProductCategory $productCategory
     * @return Response Symfony Action Response
     *
     * @Route("/newincategory/{id}", name="product_new_in_category", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function newInCategoryAction(Request $request, ProductCategory $productCategory) {
        $product = new Product();
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush($product);

            return $this->redirectToRoute('category_default', [
                'categoryslug' => $productCategory->getSlug(),
                'categoryid' => $productCategory->getId()
            ]);
        }

        return $this->render('SulmiProductBundle:Product:index_products_new_product.html.twig', array(
            'categories' => $categories,
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * It uses repository to get a list of media.
     *
     * @param Request $request
     * @param Product $product
     * @return Response Symfony Action Response
     *
     * @Route("/allimages/{id}", name="product_all_images", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function getAllImagesAction(Request $request, Product $product) {
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
     * @return Response Symfony Action Response
     *
     * @Route("/{id}", name="sulmi_product_product_show", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function showAction(Product $product) {
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
     * @return Response Symfony Action Response
     *
     * @Route("/{id}/edit", name="product_edit", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product) {
        $id = (integer) $product->getId();
        $deleteForm = $this->createDeleteForm($product);

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('SulmiProductBundle:Product')
            ->find(['id' => $id]);

        $editForm = $this->createForm('Sulmi\ProductBundle\Form\ProductType', $product);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            return $this->redirectToRoute('product_edit', [
                'id' => $id,
                'categories' => $this->sortCollection($product->getCategories(), 'id', 'asc'),
            ]);
        }
        return $this->render('SulmiProductBundle:Product:edit.html.twig', [
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'categories' => $this->sortCollection($product->getCategories(), 'title', 'asc'),
        ]);
    }

    /**
     * Deletes a product entity.
     *
     * @param Request $request
     * @param Product $product
     * @return Response Symfony Action Redirect to Product list.
     *
     * @Route("/{id}", name="product_delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product) {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush($product);
        }

        return $this->redirectToRoute('sulmi_product_index');
    }

    /**
     * Deletes a product entity with ajax.
     *
     * @param Request $request
     * @param Product $product
     * @return Response Symfony Action Redirect to Product list.
     *
     * @Route("/product_del_with_ajax/{id}", name="product_delete_ajax", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function deleteAjaxAction(Product $product) {
        $em = $this->getDoctrine()->getManager();
        $product_id=$product->getId();
        $em->remove($product);
        $em->flush();

        return $this->render('SulmiProductBundle::partial/product/product_del_with_ajax.html.twig', [
            'product' => $product_id,
        ]);
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Product $product) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
