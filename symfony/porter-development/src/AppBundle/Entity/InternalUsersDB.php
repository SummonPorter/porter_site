<?php

// src/AppBundle/Entity/InternalUsersDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
* @ORM\Entity
* @ORM\Table(name="internalUsers")
*/
class InternalUsersDB implements AdvancedUserInterface, \Serializable {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
	* @ORM\Column(type="string", length=64, name="username")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $username;

	/**
	* @ORM\Column(type="string", length=256, name="password")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $password;

	/**
	* @ORM\Column(type="string", length=64, name="passwordSalt")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $passwordSalt;

	/**
	* @ORM\Column(type="string", length=128, name="email")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $email;

	/**
	* @ORM\Column(type="string", length=32, name="permissionsGroup")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $permissionsGroup;

	/**
	* @ORM\Column(type="boolean", name="enabled")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $enabled;

	//	account lock information
	private $accountLocked;

	public function __construct() {
		$this->accountLocked = false;
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username_) {
		$this->username = $username_;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password_) {
		$this->password = $password_;
	}

	public function getPasswordSalt() {
		return $this->passwordSalt;
	}

	public function setPasswordSalt($passwordSalt_) {
		$this->passwordSalt = $passwordSalt_;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email_) {
		$this->email = $email_;
	}

	public function getPermissionsGroup() {
		return $this->permissionsGroup;
	}

	public function setPermissionsGroup($permissionsGroup_) {
		$this->permissionsGroup = $permissionsGroup_;
	}

	public function getEnabled() {
		return $this->enabled;
	}
	
	public function setEnabled($enabled_) {
		$this->enabled = $enabled_;
	}

	public function getAccountLocked() {
		return $this->accountLocked;
	}

	public function setAccountLocked($locked_) {
		$this->accountLocked = $locked_;
	}

	//========================================================================================
	//	AdvancedUserInterface
	//========================================================================================

	public function getRoles() {
		return [$this->getPermissionsGroup()];
	}

	public function getSalt() {
		//return $this->getPasswordSalt();
		return null;
	}

	public function eraseCredentials() {
	}

	public function isAccountNonExpired() {
		return true;
	}

	public function isAccountNonLocked() {
		return !$this->getAccountLocked();
	}

	public function isCredentialsNonExpired() {
		return true;
	}

	public function isEnabled() {
		return $this->getEnabled();
	}

	//========================================================================================
	//	EquatableInterface
	//========================================================================================

	public function isEqualTo(UserInterface $user_) {
        if (!$user_ instanceof InternalUsersDB)
            return false;

        if ($this->getPassword() !== $user_->getPassword())
            return false;

        if ($this->getId() !== $user_->getId())
        	return false;

        /*if ($this->getSalt() !== $user_->getSalt())
            return false;*/

        if ($this->getUsername() !== $user_->getUsername())
            return false;

        return true;
    }

	//========================================================================================
	//	Serializable
	//========================================================================================

    public function serialize() {
        return serialize([
            $this->id,
            $this->username,
            $this->password
        ]);
    }

    public function unserialize($serialized_) {
        list (
            $this->id,
            $this->username,
            $this->password
        ) = unserialize($serialized_);
    }
}