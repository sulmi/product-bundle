<?php

namespace Sulmi\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User entity uses in security implementation
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 * 
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Sulmi\ProductBundle\Repository\UserEntityRepository")
 */
class UserEntity implements UserInterface
{

    /**
     * @var int unique id 
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var type 
     */
    public $id;

    /**
     * @var string user name
     *  
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @var string user email address
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string user password
     * 
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    public $pass;

    /**
     * @var array user roles 
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * Form need magic functions
     * 
     * @return string user name
     */
    function __toString()
    {
        return $this->name;
    }

    /**
     * Get User id
     * 
     * @return int User id
     */
    function getId()
    {
        return $this->id;
    }

    /* Get User name
     *  
     * @return string
     */
    function getUsername()
    {
        return $this->name;
    }

    /**
     * Get user email
     * 
     * @return string
     */
    function getEmail()
    {
        return $this->email;
    }

    /**
     * Get passsword
     * 
     * @return string password hash
     */
    function getPassword()
    {
        return $this->pass;
    }

    /**
     * Set user name
     * 
     * @param type $name
     * @return $this
     */
    function setUsername($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set user roles
     * 
     * @param array $roles user roles array
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Set email for user
     * 
     * @param type $email
     * @return $this
     */
    function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * 
     * 
     * @param type $pass
     * @return $this
     */
    function setPassword($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return;
    }

}