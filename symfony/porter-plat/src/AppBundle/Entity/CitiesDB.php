<?php

// src/AppBundle/Entity/CitiesDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\Table(name="cities")
*/
class CitiesDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
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
	* @ORM\Column(type="string", length=64, name="timezone")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $timezone;

	/**
	* @ORM\Column(type="float", name="latitude")
	*
	* @Assert\NotNull()
	* @Assert\Type("double")
	*/
	private $latitude;

	/**
	* @ORM\Column(type="float", name="longitude")
	*
	* @Assert\NotNull()
	* @Assert\Type("double")
	*/
	private $longitude;

	/**
	* @ORM\ManyToOne(targetEntity="CountriesDB", fetch="EAGER")
	* @ORM\JoinColumn(name="countryId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $country;

	/**
	* @ORM\ManyToOne(targetEntity="RegionsDB", fetch="EAGER")
	* @ORM\JoinColumn(name="regionId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $region;

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

	public function getTimezone() {
		return $this->timezone;
	}

	public function setTimezone($timezone_) {
		$this->timezone = $timezone_;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function setLatitude($latitude_) {
		$this->latitude = $latitude_;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function setLongitude($longitude_) {
		$this->longitude = $longitude_;
	}

	public function getCountry() {
		return $this->country;
	}

	public function getCountryString() {
		if ($this->country !== null)
			return $this->country->getName();
		return '';
	}	

	public function setCountry($country_) {
		$this->country = $country_;
	}
	
	public function getRegion() {
		return $this->region;
	}

	public function getRegionString() {
		if ($this->region !== null)
			return $this->region->getName() . ' (' . $this->region->getTypeName() . ')';
		return '';
	}

	public function setRegion($region_) {
		$this->region = $region_;
	}

	//	used in forms and such to provide more information
	public function getNameAndDetails() {
		$output = $this->getName();

		if ($this->getRegion() !== null)
			$output .= ', ' . $this->getRegion()->getShortName();
		if ($this->getCountry() !== null)
			$output .= ', ' . $this->getCountry()->getName();

		return $output;
	}
	
}