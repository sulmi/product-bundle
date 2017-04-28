<?php

namespace Sulmi\ProductBundle\Repository;

use Gedmo\Translatable\Entity\Repository\TranslationRepository;

/**
 * Product repository.
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @link https://github.com/l3pp4rd/DoctrineExtensions/blob/master/lib/Gedmo/Translatable/Entity/Repository/TranslationRepository.php Translations implementation
 */
class ProductRepository extends TranslationRepository
{

    public function forCategoryAllProducts($categoryId)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT p FROM SulmiProductBundle:Product p where ";
        $query = $em->createQuery($dql);
        return $query;
    }

    public function findListAllProducts()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT p FROM SulmiProductBundle:Product p";
        $query = $em->createQuery($dql);
        return $query;
    }

    public function searchInProductsQuery($string)
    {
        $SegmentsPositive = ['like'];
        $SegmentPositive = ['not like'];
        $SegmentNegative = ['not like'];
        $SegmentsBefore = ['<', '</'];
        $SegmentsAfter = ['="'];
        $arrOut = [];
        foreach ($SegmentsBefore as $key => $segment) {
            $arrOut[] = $segment . $string;
        }


        return $arrOut;
    }

    public function searchInProductsDescription($string = '')
    {
        $em = $this->getEntityManager();
        $dql = 'select p from SulmiProductBundle:Product p where ( p.name like \'%' . $string . '%\') or (p.description like \'%' . $string . '%\') order by p.id desc';
        $query = $em->createQuery($dql);
        $data = $query->getResult();

        foreach ($data as $key => $product) {
            if (!preg_match('/' . $string . '/i', strip_tags($product->getDescription())) && !preg_match('/' . $string . '/i', strip_tags($product->getName()))) {
                unset($data[$key]);
            }
        }

        $outData = array_filter($data, function($item) {
            return $item !== null && $item !== '';
        });

        return $outData;
    }

    public function searchInProductsName($string)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery($dql);
        return $query;
    }

    public function findAllCategories($productId)
    {

        $qb = $this->createQueryBuilder('f');
        $qb->select('m')
                ->from('SulmiProductBundle:Product', 'm')
                ->where('m.mime not like \'%image%\'')
        ;
        $query = $qb->getQuery();
        $data = $query->getResult();
        return $data;
    }

}