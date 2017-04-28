<?php

namespace Sulmi\ProductBundle\Twig\Extension;

use Sulmi\ProductBundle\Helpers\TwigRoleTemplateConfigurator;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Product Twig Extension
 *
 * @author    Miroslaw Sulowski <miroslaw-sulowski@o2.pl>
 * @copyright Miroslaw Sulowski
 */
class ProductShowRole extends Twig_Extension
{

    private $template;

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('render_product_role', [$this, 'renderRole'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
                    ]),
        ];
    }

    public function renderRole(Twig_Environment $env, $userEntityRoles)
    {

        $roleName = 'ROLE_ADMIN';
        /**
         * Is not array nothin to do;
         */
        if (!is_array($userEntityRoles)) {
            return;
        }
        $roleName = $userEntityRoles[0];
        $this->template = $this->getTemplate($roleName);

        return $env->render($this->template, [
            'roleName'=>$roleName,
        ]);
    }

    public function getName()
    {
        return 'render_role';
    }

    public function getTemplate($roleName)
    {
        return TwigRoleTemplateConfigurator::recognizeTemplate($roleName);
    }

}