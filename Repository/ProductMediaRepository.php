<?php

namespace Sulmi\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PDO;

/**
 * ProductMedia repository.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class ProductMediaRepository extends EntityRepository
{

    public function findAll()
    {
        return $this->findBy([], ['id' => 'asc']);
    }

    public function findAllImages($product_id)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('m')
                ->from('SulmiProductBundle:ProductMedia', 'm')
                ->where('m.mime like \'%image%\'')
                ->andWhere('m.product = :id')
                ->setParameter('id', $product_id, PDO::PARAM_INT)
        ;
        $query = $qb->getQuery();
        $data = $query->getResult();
        return $data;
    }

    public function findAllMovies($product_id)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('m')
                ->from('SulmiProductBundle:ProductMedia', 'm')
                ->where('m.mime like \'%video%\'')
                ->andWhere('m.product = :id')
                ->setParameter('id', $product_id, PDO::PARAM_INT)
        ;
        $query = $qb->getQuery();
        $data = $query->getResult();
        return $data;
    }

    public function findAllNoImages($product_id)
    {

        $qb = $this->createQueryBuilder('f');
        $qb->select('m')
                ->from('SulmiProductBundle:ProductMedia', 'm')
                ->where('m.mime not like \'%image%\'')
                ->andWhere('m.product = :id')
                ->setParameter('id', $product_id, PDO::PARAM_INT)
        ;
        $query = $qb->getQuery();
        $data = $query->getResult();
        return $data;
    }

    public function findAllDocuments($product_id)
    {

        $qb = $this->createQueryBuilder('f');
        $qb->select('m')
                ->from('SulmiProductBundle:ProductMedia', 'm')
                ->Where('m.product = :id')
                ->andwhere('m.mime not like \'%image%\'')
                ->andWhere('m.mime not like \'%video%\'')
                ->setParameter('id', $product_id, PDO::PARAM_INT)
        ;
        $query = $qb->getQuery();
        $data = $query->getResult();
        return $data;
    }

    public function findListMovies()
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('m')
                ->from('SulmiProductBundle:ProductMedia', 'm')
                ->where('m.mime like not \'%image%\'')
        ;
        $query = $qb->getQuery();
        $data = $query->getResult();
        return $data;
    }

    public function findListImages()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m FROM SulmiProductBundle:ProductMedia m where m.mime like '%image%'";
        $query = $em->createQuery($dql);
        return $query;
    }

    public function findListNoImages()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m FROM SulmiProductBundle:ProductMedia m where m.mime like not '%image%'";
        $query = $em->createQuery($dql);
        return $query;
    }

    public function findListAllMedia()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m FROM SulmiProductBundle:ProductMedia m";
//        $dql = "SELECT m FROM SulmiProductBundle:ProductMedia m ORDER BY m.id desc, m.mime asc";
        $query = $em->createQuery($dql);
        return $query;
    }

}