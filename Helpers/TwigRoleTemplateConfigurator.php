<?php

namespace Sulmi\ProductBundle\Helpers;

/**
 * Assigns appropriate template depending on user role.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class TwigRoleTemplateConfigurator
{

    /**
     * Switch templates.
     * 
     * @param string $roleName
     * @return string Template for role
     */
    public static function recognizeTemplate($roleName)
    {
        switch ($roleName) {
            case 'ROLE_ADMIN':
                $template = 'SulmiProductBundle::partial/user/admin.html.twig';
                break;
            case 'ROLE_USER':
                $template = 'SulmiProductBundle::partial/user/user.html.twig';
                break;
            default:
                $template = 'SulmiProductBundle::partial/user/guest.html.twig';
                break;
        }
        return $template;
    }

}