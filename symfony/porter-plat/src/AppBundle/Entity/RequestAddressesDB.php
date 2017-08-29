<?php

// src/AppBundle/Entity/RequestAddressesDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CitiesDB;

/**
* @ORM\Entity
* @ORM\Table(name="requestAddresses")
*/
class RequestAddressesDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
    * @ORM\ManyToOne(targetEntity="RequestsDB", inversedBy="addresses")
    * @ORM\JoinColumn(name="requestId", referencedColumnName="id")
	*/
	private $request;

	/**
	* @ORM\ManyToOne(targetEntity="CitiesDB", fetch="EAGER")
	* @ORM\JoinColumn(name="cityId", referencedColumnName="id")
	*
	* @Assert\NotBlank()
	*/
	private $city;

	/**
	* @ORM\Column(type="string", length=256, name="address")
	*
	* @Assert\NotNull()
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
	* @ORM\Column(type="string", length=128, name="unit")
	*
	* @Assert\Type("string")
	*/
	private $unit;

	/**
	* @ORM\Column(type="string", length=64, name="context")
	*
	* @Assert\Type("string")
	*/
	private $context;

	/**
	* @ORM\Column(type="smallint", length=2, name="stepping")
	*
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $stepping;

	//	used for forms
	private $requestId;

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getRequest() {
		return $this->request;
	}

	public function setRequest(RequestsDB $request_) {
		$this->request = $request_;
		if ($request_ !== null) {
			if ($request_->getId() !== null)
				$this->setRequestId($request_->getId());
		}
	}

	public function getCity() {
		return $this->city;
	}

	public function setCity(CitiesDB $city_) {
		$this->city = $city_;
	}

	public function getAddress() {
		return $this->address;
	}

	public function setAddress(string $address_) {
		$this->address = $address_;
	}

	public function getMailing() {
		return $this->mailing;
	}

	public function setMailing(string $mailing_ = null) {
		$this->mailing = $mailing_;
	}

	public function getUnit() {
		return $this->unit;
	}

	public function setUnit(string $unit_ = null) {
		$this->unit = $unit_;
	}

	public function getContext() {
		return $this->context;
	}

	public function setContext(string $context_ = null) {
		$this->context = $context_;
	}

	public function getStepping() {
		return $this->stepping;
	}

	public function setStepping(int $stepping_) {
		$this->stepping = $stepping_;
	}

	public function getRequestId() {
		return $this->requestId;
	}

	public function setRequestId(int $requestId_) {
		$this->requestId = $requestId_;
	}

}