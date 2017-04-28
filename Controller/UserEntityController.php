<?php

namespace Sulmi\ProductBundle\Controller;

use Sulmi\ProductBundle\Entity\UserEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * CRUD actions for the user entity.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @Route("/product/users/admin")
 */
class UserEntityController extends Controller
{

    /**
     * Lists all userEntity entities.
     * 
     * @return Response Symfony Action Response
     *
     * @Route("/", name="sulmi_product_userentity_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userEntities = $em->getRepository('SulmiProductBundle:UserEntity')->findAll();

        return $this->render('SulmiProductBundle:UserEntity:index.html.twig', array(
                    'userEntities' => $userEntities,
        ));
    }

    /**
     * Creates a new userEntity entity.
     * 
     * @param Request $request
     * @return Response Symfony Action Response
     *
     * @Route("/new", name="sulmi_product_userentity_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $userEntity = new Userentity();
        $userEntity->setRoles(["ROLE_ADMIN"]);
        $form = $this->createForm('Sulmi\ProductBundle\Form\UserEntityType', $userEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordEncoder = $this->get('security.password_encoder');
            $dataPass = $form->getData()->pass;
            $encodedPassword = $passwordEncoder->encodePassword($userEntity, $dataPass);
            $userEntity->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($userEntity);
            $em->flush($userEntity);

            return $this->redirectToRoute('userentity_show', array('id' => $userEntity->getId()));
        }

        return $this->render('SulmiProductBundle:UserEntity:new.html.twig', array(
                    'userEntity' => $userEntity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a userEntity entity.
     * 
     * @param UserEntity $userEntity
     * @return Response Symfony Action Response
     *
     * @Route("/{id}", name="userentity_show", requirements={"id" = "\d+"})
     * @Method("GET")
     */
    public function showAction(UserEntity $userEntity)
    {
        $deleteForm = $this->createDeleteForm($userEntity);

        return $this->render('SulmiProductBundle:UserEntity:show.html.twig', array(
                    'userEntity' => $userEntity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing userEntity entity.
     * 
     * @param Request $request
     * @param UserEntity $userEntity
     * @return Response Symfony Action Response
     *
     * @Route("/{id}/edit", name="sulmi_product_sulmi_product_userentity_delete", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, UserEntity $userEntity)
    {
        $deleteForm = $this->createDeleteForm($userEntity);
        $editForm = $this->createForm('Sulmi\ProductBundle\Form\UserEntityType', $userEntity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sulmi_product_sulmi_product_userentity_delete', array('id' => $userEntity->getId()));
        }

        return $this->render('SulmiProductBundle:UserEntity:edit.html.twig', array(
                    'userEntity' => $userEntity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a userEntity entity.
     * 
     * @param Request $request
     * @param UserEntity $userEntity
     * @return Response Symfony Action Response
     *
     * @Route("/{id}", name="userentity_delete", requirements={"id" = "\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, UserEntity $userEntity)
    {
        $form = $this->createDeleteForm($userEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userEntity);
            $em->flush($userEntity);
        }

        return $this->redirectToRoute('sulmi_product_userentity_index');
    }

    /**
     * Creates a form to delete a userEntity entity.
     *
     * @param UserEntity $userEntity The userEntity entity
     *
     * @return Form The form
     */
    private function createDeleteForm(UserEntity $userEntity)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('userentity_delete', array('id' => $userEntity->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}