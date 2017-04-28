<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Form\ProductSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Default Search controller for frontend. 
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class ProductSearchDefaultController extends Controller
{

    /**
     * Searches the products without languages.
     *
     * @Route("/sulmi-product/productsearch/public/", name="search_default")
     * @Method({"GET","POST"})
     */
    public function productSearchAction(Request $request)
    {
        $searchForm = $this->createForm(ProductSearchType::class, [], [
            'action' => $this->generateUrl('search_default'),
            'attr' => [
                'class' => 'navbar-form navbar-left'
            ],
        ]);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $strigQuery = $searchForm->get('searchquery')->getData();
            $paterns=[
                '/[[:cntrl:]]+/'=>'',
                '/[;=]+/'=>'',
                '/[\-]{1,}/'=>'',
            ];
            $strigQuery = preg_replace(array_keys($paterns), array_values($paterns), $strigQuery);
            $strigQuery = (string) addslashes($strigQuery);
            $productsQuery = $em->getRepository('SulmiProductBundle:Product')->searchInProductsDescription($strigQuery);
            $productpagination = $this->get('knp_paginator')->paginate($productsQuery, $request->query->getInt('page', 1), 12);

            if ($request->isXmlHttpRequest()) {
                return $this->render('SulmiProductBundle::partial/product/product_paginated_ajax.html.twig', [
                            'products' => $productpagination,
                ]);
            } else {
                return $this->render('SulmiProductBundle:HomePageDefault:homePage.html.twig', [
                            'products' => $productpagination,
                            'form' => $form,
                ]);
            }
        }
        return $this->render('SulmiProductBundle:HomePageDefault:homePage.html.twig', [
                    'products' => $productpagination,
                    'form' => $searchForm,
        ]);
    }

    /**
     * Searches the products with languages.
     * 
     * @param Request $request
     * @return Response Symfony Action Response
     *
     * @Route("//productsearch/public/", name="search_default_language")
     * @Method({"GET","POST"})
     */
    public function productSearchLanguageAction(Request $request)
    {
        $searchForm = $this->createForm(ProductSearchType::class, [], [
            'action' => $this->generateUrl('search_default_language'),
            'attr' => [
                'class' => 'navbar-form navbar-left'
            ],
        ]);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $strigQuery = $searchForm->get('searchquery')->getData();
            $productsQuery = $em->getRepository('SulmiProductBundle:Product')->searchInProductsDescription($strigQuery);
            $productpagination = $this->get('knp_paginator')->paginate($productsQuery, $request->query->getInt('page', 1), 12);

            if ($request->isXmlHttpRequest()) {
                return $this->render('SulmiProductBundle::partial/product/product_paginated_ajax.html.twig', [
                            'products' => $productpagination,
                ]);
            } else {
                return $this->render('SulmiProductBundle:HomePageDefault:homePage.html.twig', [
                            'products' => $productpagination,
                            'form' => $form,
                ]);
            }
        }
        return $this->render('SulmiProductBundle:HomePageDefault:homePage.html.twig', [
                    'products' => $productpagination,
                    'form' => $searchForm,
        ]);
    }

    /**
     * Creates a form to delete a product entity.
     * @param Product $product The product entity
     *
     * @return Form The form
     */
    public function renderBlankSearchFormAction()
    {
        $searchForm = $this->createForm(ProductSearchType::class, [], [
            'action' => $this->generateUrl('search_default_language'),
            'attr' => [
                'class' => 'navbar-form navbar-left'
            ],
        ]);

        return $this->render('SulmiProductBundle:HomePageDefault:search_form.html.twig', [
                    'form' => $searchForm->createView(),
        ]);
    }

}