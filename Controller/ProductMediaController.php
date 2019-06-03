<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Controller\ProductBaseController as BaseController;
use Sulmi\ProductBundle\Entity\Product;
use Sulmi\ProductBundle\Entity\ProductCategory;
use Sulmi\ProductBundle\Entity\ProductMedia;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Basic and default backend controller.
 * CRUD Controller for ProductMedia entity.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 *
 * @Route("/productmedia/admin")
 */
class ProductMediaController extends BaseController
{

    /**
     *
     * @var UploadableManager
     */
    private $uploadableManager;

    /**
     *
     * @var Product
     */
    private $product;

    /**
     * Lists all productMedia entities.
     *
     * @param Request $request
     * @return Response Symfony Action Response
     *
     * @Route("/", name="sulmi_product_productmedia_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $productMedia = $em->getRepository('SulmiProductBundle:ProductMedia')->findListAllMedia();
        $productMediapagination = $paginator->paginate($productMedia, $request->query->getInt('page', 1), 18);

        if ($request->isXmlHttpRequest()) {
            return $this->render('SulmiProductBundle::partial/media/media_images_paginated_ajax.html.twig', [
                'productMedias' => $productMediapagination,
            ]);
        } else {
            return $this->render('SulmiProductBundle:ProductMedia:index.html.twig', [
                'productMedias' => $productMediapagination,
            ]);
        }
    }

    /**
     * You can search for all images.
     *
     * @param type $product_id
     * @return Response Symfony Action Response
     *
     * @Route("/images/{product_id}", name="productmedia_images", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function indexImagesAction($product_id)
    {
        $em = $this->getDoctrine()->getManager();
        $productMedia = $em->getRepository('SulmiProductBundle:ProductMedia')->findAllImages($product_id);

        return $this->render('SulmiProductBundle:ProductMedia:index.html.twig', array(
            'productMedia' => $productMedia,
        ));
    }

    /**
     * Lists all productMedia entities for spec product.
     *
     * @param type $product_id
     * @return Response Symfony Action Response
     *
     * @Route("/allmedia/{product_id}", name="productmedia_product", requirements={"id" = "\d+"})
     * @Method("GET")
     * Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function mediaForProductAction($product_id)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $q = $qb->select(array('m'))
            ->from('SulmiProductBundle:ProductMedia', 'm')
            ->where(
                $qb->expr()->gt('m.product', $product_id)
            )
            ->orderBy('m.id', 'asc')
            ->getQuery();

        $productMedia = $q->getArrayResult();

        return $this->render('SulmiProductBundle:ProductMedia:mediaforproduct.html.twig', array(
            'productMedia' => $productMedia,
        ));
    }

    /**
     * Saving files for the selected product.
     *
     * @param Request $request
     * @param Product $product
     * @return Response Symfony Action Response
     *
     * @Route("/ajaxnewunderproduct/{id}", name="ajax_new_under_product", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function ajaxNewUnderProductAction(Request $request, Product $product)
    {
        if ($this->uploadableManager === NULL) {
            $this->uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
        }
        $productMedia = new Productmedia();

        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductMediaType', $productMedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->product = $product;
            $this->persistMultipleFile($form, $productMedia);
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('SulmiProductBundle:ProductMedia');
            $images = $repo->findAllImages($product->getId());
            $medias = $repo->findAllDocuments($product->getId());
            $videos = $repo->findAllMovies($product->getId());

//            if (count($videos) > 0) {
//                $videos = $this->getVideoThumbnails($em, $videos);
//            }

            return $this->render('SulmiProductBundle::partial/media_slot_acordion.html.twig', [
                'product' => $product,
                'images' => $images,
                'videos' => $videos,
                'medias' => $medias,
            ]);
        }
    }

    /**
     * That means a lot of files sent as related to the product and uploaded.
     *
     * @param ProductMediaType $form
     * @param ProductMedia $productMedia
     * @return null
     */
    private function persistMultipleFile($form, $productMedia)
    {
        $pictures = $form->getData()->getPicture();

        $picturesCount = count($pictures);
        $em = $this->getDoctrine()->getManager();
        if ($picturesCount > 0) {
            $productMedia->setProduct($this->product);
            $picture = array_slice($pictures, 0, 1);
            $this->markToUploadProductMedia($productMedia, $picture[0], $em);
            $clicedPictures = array_slice($pictures, 1);

            if (count($clicedPictures) > 0) {
                foreach ($clicedPictures as $picture) {
                    $productMedia = new ProductMedia();
                    $this->markToUploadNewProductMedia($productMedia, $picture, $this->product, $em);
                }
            }
        }
        $em->flush();
    }

    /**
     * Doctrine uses the extension to save the uploaded files.
     *
     * @param ProductMedia $productMedia
     * @param File $picture
     * @param EntityManager $em
     * @return null
     */
    private function markToUploadProductMedia($productMedia, $picture, $em)
    {
        $productMedia->setPicture($picture);
        $em->persist($productMedia);
        $this->uploadableManager->markEntityToUpload($productMedia, $productMedia->getPicture());
    }

    /**
     * This means the file for writing in upload directory.
     *
     * @param ProductMedia $productMedia
     * @param File $picture
     * @param Product $product
     * @param EntityManager $em
     * @return null
     */
    private function markToUploadNewProductMedia($productMedia, $picture, $product, $em)
    {
        $productMedia->setPicture($picture);
        $productMedia->setProduct($product);
        $em->persist($productMedia);
        $this->uploadableManager->markEntityToUpload($productMedia, $productMedia->getPicture());
    }

    /**
     * Creates a new productMedia entity.
     *
     * @param Request $request
     * @return Response Symfony Action Response
     *
     * @Route("/new", name="productmedia_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if ($this->uploadableManager === NULL) {
            $this->uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
        }
        //prepare reqirments
        $productMedia = new Productmedia();
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductMediaType', $productMedia);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->persistMultipleFile($form, $productMedia);

            return $this->redirectToRoute('sulmi_product_productmedia_index', [
            ]);
        } else {//simple get request
            return $this->render('SulmiProductBundle:ProductMedia:new.html.twig', array(
                'productMedia' => $productMedia,
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * Displays a form to edit an existing productMedia entity.
     *
     * @param Request $request
     * @param ProductMedia $productMedia
     * @return Response Symfony Action Response
     *
     * @Route("/{id}/edit", name="productmedia_edit", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ProductMedia $productMedia)
    {
        $deleteForm = $this->createDeleteForm($productMedia);
        $editForm = $this->createForm('Sulmi\ProductBundle\Form\ProductMediaType', $productMedia);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('productmedia_edit', array('id' => $productMedia->getId()));
        }

        return $this->render('SulmiProductBundle:ProductMedia:edit.html.twig', array(
            'productMedia' => $productMedia,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productMedia entity.
     *
     * @param Request $request
     * @param ProductMedia $productMedia
     * @return Response Symfony Action Response redirect list all media
     *
     * @Route("/{id}", name="productmedia_delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ProductMedia $productMedia)
    {
        $form = $this->createDeleteForm($productMedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productMedia);
            $em->flush($productMedia);
        }

        return $this->redirectToRoute('sulmi_product_productmedia_index');
    }

    /**
     * Creates a form to delete a productMedia entity.
     *
     * @param ProductMedia $productMedia The productMedia entity
     *
     * @return Form The form
     */
    private function createDeleteForm(ProductMedia $productMedia)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productmedia_delete', array('id' => $productMedia->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}