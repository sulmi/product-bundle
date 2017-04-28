<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Entity\ProductCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Basic and default controller for ProductCategory.
 * Provides basic CRUD for ProductCategory entity.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @Route("/categoryajax")
 */
class ProductCategoryAjaxController extends Controller
{

    /**
     * Lists all productCategory entities.
     *
     * @Route("/", name="sulmi_product_category_index_ajax")
     * @Method("GET")
     */
    public function indexAction()
    {
        $controller = $this;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $productCat = $repo->findBy(['slug' => 'gorne-menu']);
        return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', [
        ]);
    }

    /**
     * Creates a new productCategory entity.
     *
     * @Route("/firstchild/{id}/{subcategoryid}", name="firstchild_ajax", requirements={"id" = "\d+","subcategoryid" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function firstChildOfAction(Request $request, ProductCategory $productCategory, $subcategoryid)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $subcategory = $repo->find($subcategoryid);
        $repo->persistAsLastChildOf($subcategory, $productCategory);
        $em->flush();
        return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', [
        ]);
    }

    /**
     * Creates a new productCategory entity.
     *
     * @Route("/refreshmenu", name="refreshmenu_ajax")
     * @Method({"GET", "POST"})
     */
    public function refreshMenuAction()
    {
        return $this->render('SulmiProductBundle::partial/navigation_index_refresh.html.twig', [
        ]);
    }

    /**
     * Creates a new productCategory entity.
     *
     * @Route("/siblingup/{id}/{subcategoryid}", name="sibling_up_ajax", requirements={"id" = "\d+","subcategoryid" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function siblingUpAction(Request $request, ProductCategory $productCategory, $subcategoryid)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $subcategory = $repo->find($subcategoryid);
        $repo->persistAsPrevSiblingOf($subcategory, $productCategory);
        $em->flush();
        return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', []);
    }

    /**
     * Creates a new productCategory entity.
     *
     * @Route("/sibling/{id}/{subcategoryid}", name="sibling_ajax", requirements={"id" = "\d+","subcategoryid" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function siblingAction(Request $request, ProductCategory $productCategory, $subcategoryid)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $subcategory = $repo->find($subcategoryid);
        $repo->persistAsNextSiblingOf($subcategory, $productCategory);
        $em->flush();
        return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', []);
    }

    /**
     * Creates a new productCategory entity.
     *
     * @Route("/aschild/{id}/{parentid}", name="category_as_child", requirements={"id" = "\d+","parentid" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function asChildAction(Request $request, ProductCategory $node, $parentid)
    {
        $parentid = 21;


        if ($parentid > 0) {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
            $parent = $repo->find($parentid);
            $repo->persistAsLastChildOf($node, $parent);
            $em->flush();
        }

        return $this->redirectToRoute('sulmi_product_category_index');
    }

    /**
     * Creates a new productCategory entity.
     *
     * @Route("/move_down/{id}", name="category_move_down", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function moveDownAction(Request $request, $id)
    {

        if ($id > 0) {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
            $productCategory = $repo->find($id);
            $repo->moveDown($productCategory, 1);
            $em->flush();
        }

        return $this->redirectToRoute('sulmi_product_category_index');
    }

    /**
     * Creates a new subcategory.
     *
     * @Route("/newsubcategory/{id}", name="newsubcategory_ajax", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function newSubcategoryAction(Request $request, ProductCategory $productCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');

        $entity = new ProductCategory();
        $entity->setParent($productCategory);
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductCategoryAjaxType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush($entity);

            return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', []);
        }

        return $this->render('SulmiProductBundle::partial/product_subcategory_ajax_form.html.twig', [
                    'productCategory' => $entity,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new subcategory.
     *
     * @Route("/newcategory", name="newcategory_ajax")
     * @Method({"GET", "POST"})
     */
    public function newCategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $productCategory = $repo->findOneBy(['slug' => 'gorne-menu']);
        if ($productCategory === null) {
            $productCategory = new ProductCategory();
            $productCategory->setTitle('Górne menu');
            $em->persist($productCategory);
            $em->flush();
            $productCategory->setTitle('Top menu')
                    ->setTranslatableLocale('en');
            $em->persist($productCategory);
            $em->flush();
        }
        $entity = new ProductCategory();
        $entity->setParent($productCategory);
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductCategoryType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush($entity);

            return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', []);
        }

        return $this->render('SulmiProductBundle::partial/product_category_ajax_form.html.twig', [
                    'productCategory' => $entity,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new productCategory entity.
     *
     * @Route("/new", name="category_new_ajax")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $productCategory = $repo->findOneBy(['slug' => 'gorne-menu']);
        if ($productCategory === null) {

            $productCategory = new ProductCategory();
            $productCategory->setTitle('Górne menu');
            $em->persist($productCategory);
            $em->flush();
            $productCategory->setTitle('Top menu')
                    ->setTranslatableLocale('en');
            $em->persist($productCategory);
            $em->flush();
        }
        $entity = new ProductCategory();
        $entity->setParent($productCategory);
        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductCategoryType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush($entity);

            return $this->redirectToRoute('sulmi_product_category_index');
        }

        return $this->render('SulmiProductBundle:ProductCategory:new.html.twig', array(
                    'productCategory' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a productCategory entity.
     * 
     * @Route("/{id}", name="category_show", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function showAction(ProductCategory $productCategory)
    {
        $deleteForm = $this->createDeleteForm($productCategory);

        return $this->render('SulmiProductBundle:ProductCategory:show.html.twig', array(
                    'productCategory' => $productCategory,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing productCategory entity.
     *
     * @Route("/{id}/edit", name="editcategory_ajax", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ProductCategory $productCategory)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('Sulmi\ProductBundle\Form\ProductCategoryAjaxType', $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productCategory);
            $em->flush($productCategory);

            return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', []);
        }

        return $this->render('SulmiProductBundle::partial/product_editcategory_ajax_form.html.twig', [
                    'productCategory' => $productCategory,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a productCategory entity.
     *
     * @Route("/removefromtree/{id}", name="removefromtree_ajax", requirements={"id" = "\d+"})
     * @Method("post")
     */
    public function removeFromTreeAction(Request $request, ProductCategory $productCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $products = $productCategory->getProducts();
        if ($products->count() > 0) {
            $products->clear();
            $em->persist($productCategory);
            $em->flush($productCategory);
        }
        $repo->removeFromTree($productCategory);
        $em->flush();
        return $this->render('SulmiProductBundle::partial/navigation_index.html.twig', []);
    }

    /**
     * Creates a form to delete a productCategory entity.
     *
     * @param ProductCategory $productCategory The productCategory entity
     *
     * @return Form The form
     */
    private function createDeleteForm(ProductCategory $productCategory)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('category_delete', array('id' => $productCategory->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}