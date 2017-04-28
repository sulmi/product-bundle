<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Entity\ProductCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Productcategory backend controller.
 * 
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @Route("/category/admin")
 */
class ProductCategoryController extends Controller
{
    /**
     * response Navigation html .
     * 
     * @link https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/tree.md#create-html-tree Retrieving as html tree
     * @param Request $request Symfony Request object
     * @param string $slug Slug name for selected root tree navigation
     * @return Response Html tree for root neme navigation
     *
     * @Route("/getnavigation/{slug}", name="category_navigation", requirements={"slug" = "[\w\-_]+"})
     * @Method("GET")
     */
    public function getNavigationAction(Request $request, $slug)
    {
        $controller = $this;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $root = $repo->findOneBy([
            'slug' => $slug
        ]);
        $navStr = $repo->childrenHierarchy($root, false, [
            'decorate' => false,
            'rootOpen' => function($children) {
                return '<li>';
//                return '<ul' . $classDropdownMenu . '>';
            },
            'rootClose' => '</li>\n',
            'childStart' => '<ul>\n',
            'childClose' => '</ul>\n',
            'nodeDecorator' => function($node) use (&$controller) {
                return '<li><a href="' . $controller->generateUrl($node['routename']) . '">' . $node['title'] . '</a><li>\n';
            }
        ]);
        $this->getNavigationAction($request, $slug);
        $rootNodes = $repo->getRootNodes();
        $countRootNodes = count($rootNodes);
        if ($countRootNodes > 0) {
            foreach ($rootNodes as $node) {
                $children = $repo->getChildren($node, true);
                $countChildren = count($children);
                if ($countChildren > 0) {
                    $classRoot = ' class="dropdown"';
                    $classDropdownMenu = ' class="dropdown-menu"';
                    $navStr[] = '<li' . $classRoot . '>\n';
                    $navStr[] = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $node->getTitle() . ' <span class="caret"></span></a>\n';
                } else {
                    $title = $node->getTitle();
                    $classDropdownMenu = '';
                    $classRoot = '';
                    $navStr[] = '<li' . $classRoot . '><a href="' . $this->generateUrl($node->getRoutename()) . '">' . $title . '</a>\n';
                }

                $navStr = $repo->childrenHierarchy(null, false, [
                    'decorate' => true,
                    'rootOpen' => function($children) {
                        return '<li>';
                    },
                    'rootClose' => '</li>\n',
                    'childStart' => '<ul>\n',
                    'childClose' => '</ul>\n',
                    'nodeDecorator' => function($node) use (&$controller) {
                        return '<li><a href="' . $controller->generateUrl($node['routename']) . '">' . $node['title'] . '</a><li>\n';
                    }
                ]);

                $navStr[] = '</li>';
            }
        }

        $productCategories = $navStr;

        return $this->render('SulmiProductBundle:ProductCategory:nav.html.twig', [
                    'productCategories' => $productCategories,
        ]);
    }

    /**
     * Lists all productCategory entities.
     *
     * @return Response Symfony Action Response
     * 
     * @Route("/", name="sulmi_product_category_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $controller = $this;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $productCat = $repo->findAll();
        $productCategories = $repo->findAll();
        return $this->render('SulmiProductBundle:ProductCategory:index.html.twig', [
                    'productCategories' => $productCategories,
                    'productCat' => $productCat,
        ]);
    }

    /**
     * Moves one place up the node category.
     * 
     * @param Request $request
     * @param integer $id
     * @return Response Response Symfony Action Response
     *
     * @Route("/move_up/{id}", name="category_move_up", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function moveUpAction(Request $request, $id)
    {

        if ($id > 0) {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('SulmiProductBundle:ProductCategory');
            $productCategory = $repo->find($id);
            $repo->moveUp($productCategory, 1);
            $em->flush();
        }

        return $this->redirectToRoute('sulmi_product_category_index');
    }

    /**
     * It allows you to give the relationship as a sub for the indicated category.
     * 
     * @param Request $request
     * @param ProductCategory $node Category id as child
     * @param int $parentid Category id as parent
     * @return Response Symfony Action Response redirect to route all category
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
     * Moves one position down node category.
     * 
     * @param Request $request
     * @param integer $id Category index id to move down action
     * @return Response Symfony Action Response redirect to index all categories
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
     * Creates a new productCategory entity.
     * 
     * @param Request $request
     * @return Response Symfony Action Response
     * 
     * @Route("/new", name="category_new")
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

            $productCategory->setTitle('De Top menu')
                    ->setTranslatableLocale('de');
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
     * @param ProductCategory $productCategory
     * @return Response Symfony Action Response
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
     * @param Request $request
     * @param ProductCategory $productCategory
     * @return Response Symfony Action Response
     *
     * @Route("/{id}/edit", name="category_edit", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ProductCategory $productCategory)
    {
        $deleteForm = $this->createDeleteForm($productCategory);
        $editForm = $this->createForm('Sulmi\ProductBundle\Form\ProductCategoryType', $productCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_edit', array('id' => $productCategory->getId()));
        }

        $productCategoryslug = $productCategory->getSlug();
        if ($productCategoryslug == '') {
            $productCategory->setSlug('slug');
        }
        return $this->render('SulmiProductBundle:ProductCategory:edit.html.twig', array(
                    'productCategory' => $productCategory,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a productCategory entity.
     * 
     * @param Request $request
     * @param ProductCategory $productCategory
     * @return Response Symfony Action Response
     *
     * @Route("/{id}", name="category_delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ProductCategory $productCategory)
    {
        $form = $this->createDeleteForm($productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productCategory);
            $em->flush($productCategory);
        }

        return $this->redirectToRoute('sulmi_product_category_index');
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