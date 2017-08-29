<?php

// src/AppBundle/Entity/CustomersDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\DiscountsDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use \DateTime;

/**
* @ORM\Entity
* @ORM\Table(name="customers")
*/
class CustomersDB implements AdvancedUserInterface, \Serializable {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
	* @ORM\Column(type="boolean", name="guest")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $guest;

	/**
    * @ORM\Column(type="string", length=128, name="email")
	*
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $email;

	/**
	* @ORM\Column(type="string", length=256, name="password")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $password;

	/**
	* @ORM\Column(type="string", length=32, name="permissionsGroup")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $permissionsGroup;

	/**
    * @ORM\Column(type="string", length=64, name="firstName")
	*
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $firstName;

	/**
    * @ORM\Column(type="string", length=64, name="lastName")
	*
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $lastName;

	/**
	* @ORM\Column(type="string", length=32, name="mobilePhone")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $mobilePhone;

	/**
	* @ORM\Column(type="string", length=32, name="homePhone")
	*
	* @Assert\Type("string")
	*/
	private $homePhone;

	/**
    * @ORM\ManyToOne(targetEntity="CitiesDB", fetch="EAGER")
    * @ORM\JoinColumn(name="cityId", referencedColumnName="id")
	*/
	private $city;

	/**
    * @ORM\Column(type="string", length=256, name="address")
    *
	* @Assert\Type("string")
	*/
	private $address;

	/**
    * @ORM\Column(type="string", length=10, name="mailing")
	*
	* @Assert\Type("string")
	*/
	private $mailing;

	/**
	* @ORM\Column(type="integer", length=11, name="signupEpoch")
	*
	* @Assert\Type("integer")
	*/
	private $signupEpoch;

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

	public function getGuest() {
		return $this->guest;
	}

	public function setGuest($guest_) {
		$this->guest = $guest_;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email_) {
		$this->email = $email_;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password_) {
		$this->password = $password_;
	}

	public function getPermissionsGroup() {
		return $this->permissionsGroup;
	}

	public function setPermissionsGroup($permissionsGroup_) {
		$this->permissionsGroup = $permissionsGroup_;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function setFirstName($firstName_) {
		$this->firstName = $firstName_;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function setLastName($lastName_) {
		$this->lastName = $lastName_;
	}

	public function getMobilePhone() {
		return $this->mobilePhone;
	}

	public function setMobilePhone($mobilePhone_) {
		$this->mobilePhone = $mobilePhone_;
	}

	public function getHomePhone() {
		return $this->homePhone;
	}

	public function setHomePhone($homePhone_) {
		$this->homePhone = $homePhone_;
	}

	public function getCity() {
		return $this->city;
	}

	public function setCity($city_) {
		$this->city = $city_;
	}

	public function getAddress() {
		return $this->address;
	}

	public function setAddress($address_) {
		$this->address = $address_;
	}

	public function getMailing() {
		return $this->mailing;
	}

	public function setMailing($mailing_) {
		$this->mailing = $mailing_;
	}

	public function getSignupEpoch() {
        return $this->signupEpoch;
    }

    public function setSignupEpoch($signupEpoch_) {
        $this->signupEpoch = $signupEpoch_;
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
	//	Misc
	//========================================================================================

	public function getFullName() {
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

	public function getSignupEpochString() {
        if ($this->getSignupEpoch() === null)
            return 0;
        if ($this->getSignupEpoch() === 0)
            return 0;
        $date = DateTime::createFromFormat('U', $this->getSignupEpoch());
        return $date->format('Y-m-d H:i:s');
    }

	//========================================================================================
	//	AdvancedUserInterface
	//========================================================================================

    public function getUsername() {
		return $this->getEmail();
	}

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