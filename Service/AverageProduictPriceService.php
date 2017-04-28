<?php

namespace Sulmi\ProductBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Average Price Product.
 * Basic service example.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class AverageProductPriceService {

 private $em;

 function __construct(EntityManager $em) {
  $this->em = $em->getRepository('SulmiProductBundle:Product');
 }

 function getEm() {
  return $this->em;
 }

 function setEm($em) {
  
  $this->em = $em;
  return $this;
 }
 public function getavg() {
  return $this->em->queryAvg();
 }

}
