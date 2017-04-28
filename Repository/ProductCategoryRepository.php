<?php

namespace Sulmi\ProductBundle\Repository;

use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * ProductCategory repository.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class ProductCategoryRepository extends NestedTreeRepository
{

    /**
     * {@inheritDoc}
     */
    public function getNodesHierarchyQuery($node = null, $direct = false, array $options = array(), $includeNode = false)
    {
        $query = $this->getNodesHierarchyQueryBuilder($node, $direct, $options, $includeNode)->getQuery();
        $query->setHint(
                Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        return $query;
    }

}