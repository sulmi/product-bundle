<?php

namespace Sulmi\ProductBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sulmi\ProductBundle\Entity\UserEntity;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the sample data to load in the database when running the unit and
 * functional tests. Execute this command to load the data:
 *
 *   $ php bin/console doctrine:fixtures:load
 *
 * See http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class LoadFixtures implements FixtureInterface, ContainerAwareInterface {

    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager) {
        $this->loadUsers($manager);
    }

    private function loadUsers(ObjectManager $manager) {
        $passwordEncoder = $this->container->get('security.password_encoder');

        for ($index = 1; $index < 3; $index++) {
            $someUser = new UserEntity();
            $someUser->setUsername('u' . $index);
            $someUser->setRoles(['ROLE_ADMIN']);
            $someUser->setEmail('u' . $index . '@dom.com');
            $encodedPassword = $passwordEncoder->encodePassword($someUser, 'pass');
            $someUser->setPassword($encodedPassword);
            $manager->persist($someUser);
        }
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

}
