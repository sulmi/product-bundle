<?php

namespace Sulmi\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Controller used to manage the product application security.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @Route("//admin/product")
 */
class SecurityController extends Controller
{

    /**
     * The default login action.
     * 
     * @return Response Symfony Action Response
     * 
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');
        return $this->render('SulmiProductBundle:Security:login.html.twig', [
                    'last_username' => $helper->getLastUsername(),
                    // last authentication error (if any)
                    'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in app/config/security.yml
     *
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('This should never be reached!');
    }

}