<?php

namespace Sulmi\ProductBundle\Menu;

use Sulmi\ProductBundle\Entity\ProductCategory;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Menu Builder
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class Builder implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    private $menu;
    private $repo;

    public function mesterMenu(FactoryInterface $factory, array $options)
    {
        $root = $this->growNavigation('gorne-menu', $factory); //Sulmi\ProductBundle\Entity\ProductCategory                            
        $root = $this->getRootDefaults($root);
        $nodeChildrens = $this->repo->childrenHierarchy($root, false, []); //array of childreens

        if (count($nodeChildrens) > 0) {
            foreach ($nodeChildrens as $noderoot) {
                $this->getMenuTreeAdapter($this->menu, $noderoot);
            }
        }

        return $this->menu;
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {

        $root = $this->growNavigation('gorne-menu', $factory); //Sulmi\ProductBundle\Entity\ProductCategory                            
        $root = $this->getRootDefaults($root);
        $nodeChildrens = $this->repo->childrenHierarchy($root, false, []); //array of childreens

        if (count($nodeChildrens) > 0) {
            foreach ($nodeChildrens as $noderoot) {
                $this->getMenuTreeAdapter($this->menu, $noderoot);
            }
        }

        return $this->menu;
    }

    public function getMenuTreeAdapter($parent, $node)
    {

        $node = $this->getDefaults($node);
        $route = $this->prepareRoute($node);
        $parent = $parent->addChild($node['slug'], [
            'label' => $node['title'],
            'route' => $route['route'],
            'routeParameters' => $route['routeParameters'],
        ]);

        if (count($node['__children']) > 0) {
            foreach ($node['__children'] as $child) {
                $this->getMenuTreeAdapter($parent, $child);
            }
        }
    }

    public function prepareRoute($node)
    {

        $node = $this->getDefaults($node);
        $route = $node['routename'];
        $routeParameters = [];
        if (strlen($route) < 1) {
            $route = 'category_default_language';
//            $route = 'category_default';
            $routeParameters = [
                'id' => $node['id'],
                'categoryslug' => $node['slug'],
            ];
        }
        return [
            'route' => $route,
            'routeParameters' => $routeParameters,
        ];
    }

    public function getMenuTreeAdminAdapter($parent, $node)
    {
        $route = $this->prepareAdminRoute($node);

        $parent = $parent->addChild($node['slug'], [
            'label' => $node['title'],
            'route' => $route['route'],
            'routeParameters' => $route['routeParameters'],
        ]);

        if (count($node['__children']) > 0) {
            foreach ($node['__children'] as $child) {
                $this->getMenuTreeAdminAdapter($parent, $child);
            }
        }
    }

    public function prepareAdminRoute($node)
    {
        $route = 'category_default_language';
//        $route = 'category_default';
        $routeParameters = [
            'id' => $node['id'],
            'categoryslug' => $node['slug'],
        ];
        return [
            'route' => $route,
            'routeParameters' => $routeParameters,
        ];
    }

    public function getDefaults($node)
    {
        if (strlen($node['slug']) < 1) {
            $node['slug'] = 'none-slug';
        }
        if (strlen($node['title']) < 1) {
            $node['title'] = 'None Title';
        }
        return $node;
    }

    public function getRootDefaults($root)
    {

        if (strlen($root->getSlug()) < 1) {
            $root->setSlug('none-slug');
        }

        if (strlen($root->getTitle()) < 1) {
            $root->getTitle('None Title');
        }
        return $root;
    }

    public function growNavigation($slug, $factory)
    {
        $this->menu = $factory->createItem('root', [
            'childrenAttributes' => ['class' => 'nav navbar-nav']
        ]);

        $em = $this->container->get('doctrine')->getManager();
        $this->repo = $em->getRepository('SulmiProductBundle:ProductCategory');
        $root = $this->repo->findOneBy([
            'slug' => $slug
        ]);

        if ($root == null) {
            $root = new ProductCategory();
            $root->setTitle('Top');
            $root->setSlug('top');
            $root->setId(1);
        }

        return $root;
    }

}