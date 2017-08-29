<?php

// src/AppBundle/Entity/DiscountsDB.php
namespace AppBundle\Entity;

use AppBundle\Entity\ServicesDB;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;

/**
* @ORM\Entity
* @ORM\Table(name="discounts")
*/
class DiscountsDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
	* @ORM\Column(type="string", length=32, name="code")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $code;

	/**
	* @ORM\Column(type="string", length=128, name="name")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $name;

	/**
	* @ORM\Column(type="string", length=256, name="description")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $description;

	/**
	* @ORM\Column(type="integer", length=11, name="createdEpoch")
	*
	* @Assert\Type("integer")
	*/
	private $createdEpoch;

	/**
	* @ORM\Column(type="integer", length=11, name="startEpoch")
	*
	* @Assert\NotBlank()
	* @Assert\Type("integer")
	*/
	private $startEpoch;

	/**
	* @ORM\Column(type="integer", length=11, name="endEpoch")
	*
	* @Assert\NotBlank()
	* @Assert\Type("integer")
	*/
	private $endEpoch;

	/**
	* @ORM\Column(type="smallint", length=5, name="discount")
	*
	* @Assert\Type("integer")
    * @Assert\Range(
    * 		min = 1,
    *		minMessage = "Min discount is 1"
    *		)
    */
	private $discount;

	/**
	* @ORM\Column(type="string", length=64, name="discountType")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $discountType;

	/**
    * @ORM\ManyToMany(targetEntity="ServicesDB", fetch="EAGER", cascade={"persist", "merge"})
    * @ORM\JoinTable(name="discountServiceWhitelist",
    *		joinColumns={@ORM\JoinColumn(name="discountId", referencedColumnName="id")},
    *		inverseJoinColumns={@ORM\JoinColumn(name="serviceId", referencedColumnName="id")}
    *		)
    */
	private $whitelistServices;

	/**
    * @ORM\ManyToMany(targetEntity="CountriesDB", fetch="EAGER", cascade={"persist", "merge"})
    * @ORM\JoinTable(name="discountCountryBlacklist",
    *		joinColumns={@ORM\JoinColumn(name="discountId", referencedColumnName="id")},
    *		inverseJoinColumns={@ORM\JoinColumn(name="countryId", referencedColumnName="id")}
    *		)
    */
	private $blacklistCountries;

	/**
    * @ORM\ManyToMany(targetEntity="RegionsDB", fetch="EAGER", cascade={"persist", "merge"})
    * @ORM\JoinTable(name="discountRegionBlacklist",
    *		joinColumns={@ORM\JoinColumn(name="discountId", referencedColumnName="id")},
    *		inverseJoinColumns={@ORM\JoinColumn(name="regionId", referencedColumnName="id")}
    *		)
    */
	private $blacklistRegions;

	/**
    * @ORM\ManyToMany(targetEntity="CitiesDB", fetch="EAGER", cascade={"persist", "merge"})
    * @ORM\JoinTable(name="discountCityBlacklist",
    *		joinColumns={@ORM\JoinColumn(name="discountId", referencedColumnName="id")},
    *		inverseJoinColumns={@ORM\JoinColumn(name="cityId", referencedColumnName="id")}
    *		)
    */
	private $blacklistCities;
	
	public function __construct() {
		$this->createdEpoch = $this->startEpoch = $this->endEpoch = 0;
		$this->whitelistServices = new ArrayCollection();
		$this->blacklistCountries = new ArrayCollection();
		$this->blacklistRegions = new ArrayCollection();
		$this->blacklistCities = new ArrayCollection();
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

	public function getCode() {
		return $this->code;
	}

	public function setCode($code_) {
		$this->code = $code_;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name_) {
		$this->name = $name_;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description_) {
		$this->description = $description_;
	}

	public function getCreatedEpoch() {
		return $this->createdEpoch;
	}

	public function setCreatedEpoch($createdEpoch_) {
		$this->createdEpoch = $createdEpoch_;
	}

	public function getStartEpoch() {
		return $this->startEpoch;
	}

	public function setStartEpoch($startEpoch_) {
		$this->startEpoch = $startEpoch_;
	}

	public function getEndEpoch() {
		return $this->endEpoch;
	}

	public function setEndEpoch($endEpoch_) {
		$this->endEpoch = $endEpoch_;
	}

	public function getDiscount() {
		return $this->discount;
	}

	public function setDiscount($discount_) {
		$this->discount = $discount_;
	}

	public function getDiscountType() {
		return $this->discountType;
	}

	public function setDiscountType($discountType_) {
		$this->discountType = $discountType_;
	}

	public function getWhitelistServices() {
		if ($this->whitelistServices instanceof PersistentCollection)
			return $this->whitelistServices->getValues();
		if ($this->whitelistServices instanceof ArrayCollection)
			return $this->whitelistServices->getValues();
		return $this->whitelistServices;
	}

	public function setWhitelistServices($whitelistServices_) {
		$this->whitelistServices = $whitelistServices_;
	}

	public function getBlacklistCountries() {
		if ($this->blacklistCountries instanceof PersistentCollection)
			return $this->blacklistCountries->getValues();
		if ($this->blacklistCountries instanceof ArrayCollection)
			return $this->blacklistCountries->getValues();
		return $this->blacklistCountries;
	}

	public function setBlacklistCountries($blacklistCountries_) {
		$this->blacklistCountries = $blacklistCountries_;
	}

	public function getBlacklistRegions() {
		if ($this->blacklistRegions instanceof PersistentCollection)
			return $this->blacklistRegions->getValues();
		if ($this->blacklistRegions instanceof ArrayCollection)
			return $this->blacklistRegions->getValues();
		return $this->blacklistRegions;
	}

	public function setBlacklistRegions($blacklistRegions_) {
		$this->blacklistRegions = $blacklistRegions_;
	}

	public function getBlacklistCities() {
		if ($this->blacklistCities instanceof PersistentCollection)
			return $this->blacklistCities->getValues();
		if ($this->blacklistCities instanceof ArrayCollection)
			return $this->blacklistCities->getValues();
		return $this->blacklistCities;
	}

	public function setBlacklistCities($blacklistCities_) {
		$this->blacklistCities = $blacklistCities_;
	}

	//========================================================================================
	//	Misc
	//========================================================================================

	public function getCreatedEpochString() {
		$date = DateTime::createFromFormat('U', $this->getCreatedEpoch());
		return $date->format('Y-m-d H:i:s');
	}

	public function getStartEpochString() {
		$date = DateTime::createFromFormat('U', $this->getStartEpoch());
		return $date->format('Y-m-d H:i:s');
	}

	public function getEndEpochString() {
		$date = DateTime::createFromFormat('U', $this->getEndEpoch());
		return $date->format('Y-m-d H:i:s');
	}

	public function getWhitelistServicesString() {
		$services = $this->getWhitelistServices();
		$output = '';
		foreach ($services as $service)
			$output .= $service->getName() . '<br>';
		return $output;
	}

	public function hasWhitelistService(ServicesDB $service_): bool {
		$services = $this->getWhitelistServices();
		foreach ($services as $service) {
			if ($service->getId() === $service_->getId())
				return true;
		}

		return false;
	}

	public function hasWhitelistServiceId(int $serviceId_): bool {
		$services = $this->getWhitelistServices();
		foreach ($services as $service) {
			if ($service->getId() === $serviceId_)
				return true;
		}

		return false;
	}

	public function getBlacklistCountriesString() {
		$countries = $this->getBlacklistCountries();
		$output = '';
		foreach ($countries as $country)
			$output .= $country->getName() . '<br>';
		return $output;
	}

	public function getBlacklistRegionsString() {
		$regions = $this->getBlacklistRegions();
		$output = '';
		foreach ($regions as $region)
			$output .= $region->getName() . '<br>';
		return $output;
	}

	public function getBlacklistCitiesString() {
		$cities = $this->getBlacklistCities();
		$output = '';
		foreach ($cities as $city)
			$output .= $city->getName() . '<br>';
		return $output;
	}

	public function isPercentage() {
		return $this->getDiscountType() === 'percentage';
	}

	public function isFlat() {
		return $this->getDiscountType() === 'flat';
	}

}