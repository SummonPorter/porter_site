<?php

// src/AppBundle/Entity/CountriesDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\Table(name="countries")
*/
class CountriesDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="smallint", length=5, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
	* @ORM\Column(type="boolean", name="enabled")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $enabled;

	/**
	* @ORM\Column(type="string", length=128, name="name")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $name;

	/**
	* @ORM\Column(type="string", length=5, name="shortName")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $shortName;

	/**
	* @ORM\Column(type="string", length=5, name="currencyCode")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $currencyCode;

	public function __construct() {
		$this->enabled = false;
		$this->name = '';
		$this->shortName = '';
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

	public function getEnabled() {
		return $this->enabled;
	}

	public function setEnabled($enabled_) {
		$this->enabled = $enabled_;
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

	public function getCurrencyCode() {
		return $this->currencyCode;
	}

	public function setCurrencyCode($currencyCode_) {
		$this->currencyCode = $currencyCode_;
	}

}