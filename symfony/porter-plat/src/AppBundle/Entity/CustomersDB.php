<?php

// src/AppBundle/Entity/CustomersDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\DiscountsDB;
use \DateTime;

/**
* @ORM\Entity
* @ORM\Table(name="customers")
*/
class CustomersDB {

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
    * @ORM\Column(type="string", length=512, name="creditToken")
	*
	* @Assert\Type("string")
	*/
	private $creditToken;

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

	public function getCreditToken() {
		return $this->creditToken;
	}

	public function setCreditToken($creditToken_) {
		$this->creditToken = $creditToken_;
	}
	
	//========================================================================================
	//	Misc
	//========================================================================================

	public function getFullName() {
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

}