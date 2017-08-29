<?php

// src/AppBundle/Entity/RegionsDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\Table(name="regions")
*/
class RegionsDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
	* @ORM\Column(type="string", length=25, name="typeName")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $typeName;

	/**
	* @ORM\Column(type="string", length=100, name="name")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $name;

	/**
	* @ORM\Column(type="string", length=100, name="shortName")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $shortName;

	/**
	* @ORM\ManyToOne(targetEntity="CountriesDB", fetch="EAGER")
	* @ORM\JoinColumn(name="countryId", referencedColumnName="id")
	*
	* @Assert\NotBlank()
	*/
	private $country;

	/**
	* @ORM\Column(type="float", name="requestTax")
	*
	* @Assert\NotNull()
	* @Assert\Type("float")
	*/
	private $requestTax;

	/**
	* @ORM\Column(type="float", name="porterTax")
	*
	* @Assert\NotNull()
	* @Assert\Type("float")
	*/
	private $porterTax;

	/**
	* @ORM\Column(type="boolean", name="enabled")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $enabled;

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getTypeName() {
		return $this->typeName;
	}

	public function setTypeName($typeName_) {
		$this->typeName = $typeName_;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name_) {
		$this->name = $name_;
	}

	public function getShortName() {
		return $this->shortName;
	}

	public function setShortName($shortName_) {
		$this->shortName = $shortName_;
	}

	public function getCountry() {
		return $this->country;
	}

	public function setCountry($country_) {
		$this->country = $country_;
	}

	public function getRequestTax() {
		return $this->requestTax;
	}

	public function setRequestTax($requestTax_) {
		$this->requestTax = $requestTax_;
	}

	public function getPorterTax() {
		return $this->porterTax;
	}

	public function setPorterTax($porterTax_) {
		$this->porterTax = $porterTax_;
	}

	public function getEnabled() {
		return $this->enabled;
	}

	public function setEnabled($enabled_) {
		$this->enabled = $enabled_;
	}

}