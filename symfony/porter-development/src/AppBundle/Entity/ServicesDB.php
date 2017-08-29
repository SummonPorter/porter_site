<?php

// src/AppBundle/Entity/ServicesDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \JsonSerializable;

/**
* @ORM\Entity
* @ORM\Table(name="services")
*/
class ServicesDB implements JsonSerializable {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

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
	* @ORM\Column(type="smallint", length=3, name="addressCount")
	*
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $addressCount;

	/**
	* @ORM\Column(type="string", length=32, name="feeRate")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $feeRate;

	/**
	* @ORM\Column(type="float", name="fee")
	*
	* @Assert\NotNull()
	* @Assert\Type("float")
	*/
	private $fee;

	/**
	* @ORM\Column(type="float", name="baseFee")
	*
	* @Assert\NotNull()
	* @Assert\Type("float")
	*/
	private $baseFee;

	/**
	* @ORM\Column(type="float", name="pay")
	*
	* @Assert\NotNull()
	* @Assert\Type("float")
	*/
	private $pay;

	/**
	* @ORM\Column(type="float", name="trustFee")
	*
	* @Assert\NotNull()
	* @Assert\Type("float")
	*/
	private $trustFee;

	/**
	* @ORM\Column(type="integer", length=8, name="defaultEstDuration")
	*
	* @Assert\Type("integer")
	*/
	private $defaultEstDuration;

	/**
	* @ORM\Column(type="string", length=256, name="webHeader")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $webHeader;

	/**
	* @ORM\Column(type="string", length=256, name="webMobileHeader")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $webMobileHeader;

	/**
	* @ORM\Column(type="string", length=256, name="webIcon")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $webIcon;

	/**
	* @ORM\Column(type="string", length=256, name="appImage")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $appImage;

	/**
	* @ORM\Column(type="boolean", name="enabled")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $enabled;

	/**
    * @ORM\ManyToOne(targetEntity="ServiceGroupsDB", inversedBy="services")
    * @ORM\JoinColumn(name="groupId", referencedColumnName="id")
	*/
	private $group;

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
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

	public function getAddressCount() {
		return $this->addressCount;
	}

	public function setAddressCount($addressCount_) {
		$this->addressCount = $addressCount_;
	}

	public function getFeeRate() {
		return $this->feeRate;
	}

	public function setFeeRate($feeRate_) {
		$this->feeRate = $feeRate_;
	}

	public function getFee() {
		return $this->fee;
	}

	public function setFee($fee_) {
		$this->fee = $fee_;
	}

	public function getBaseFee() {
		return $this->baseFee;
	}

	public function setBaseFee($baseFee_) {
		$this->baseFee = $baseFee_;
	}

	public function getPay() {
		return $this->pay;
	}

	public function setPay($pay_) {
		return $this->pay = $pay_;
	}

	public function getTrustFee() {
		return $this->trustFee;
	}

	public function setTrustFee($trustFee_) {
		$this->trustFee = $trustFee_;
	}

	public function getDefaultEstDuration() {
		return $this->defaultEstDuration;
	}

	public function setDefaultEstDuration($defaultEstDuration_) {
		$this->defaultEstDuration = $defaultEstDuration_;
	}

	public function getWebHeader() {
		return $this->webHeader;
	}

	public function setWebHeader($webHeader_) {
		$this->webHeader = $webHeader_;
	}

	public function getWebMobileHeader() {
		return $this->webMobileHeader;
	}

	public function setWebMobileHeader($webMobileHeader_) {
		$this->webMobileHeader = $webMobileHeader_;
	}

	public function getWebIcon() {
		return $this->webIcon;
	}

	public function setWebIcon($webIcon_) {
		$this->webIcon = $webIcon_;
	}

	public function getAppImage() {
		return $this->appImage;
	}

	public function setAppImage($appImage_) {
		$this->appImage = $appImage_;
	}

	public function getEnabled() {
		return $this->enabled;
	}

	public function setEnabled($enabled_) {
		$this->enabled = $enabled_;
	}

	public function getGroup() {
		return $this->group;
	}

	public function setGroup($group_) {
		$this->group = $group_;
	}

	//========================================================================================
	//	Misc
	//========================================================================================

	public function isPerHour() {
		return $this->getFeeRate() === 'hour';
	}

	public function isPerMinute() {
		return $this->getFeeRate() === 'minute';
	}

	public function jsonSerialize() {
		$groupData = [];

		if ($this->getGroup() !== null) {
			$groupData['id'] 			= $this->getGroup()->getId();
			$groupData['name'] 			= $this->getGroup()->getName();
			$groupData['description'] 	= $this->getGroup()->getDescription();
			$groupData['webImage'] 		= $this->getGroup()->getWebImage();
			$groupData['appImage'] 		= $this->getGroup()->getAppImage();
		}

        return ['id' 					=> $this->getId(),
        		'name' 					=> $this->getName(),
        		'description' 			=> $this->getDescription(),
        		'addressCount'			=> $this->getAddressCount(),
        		'webHeader' 			=> $this->getWebHeader(),
        		'webMobileHeader' 		=> $this->getWebMobileHeader(),
        		'webIcon' 				=> $this->getWebIcon(),
        		'appImage' 				=> $this->getAppImage(),
        		'feeRate' 				=> $this->getFeeRate(),
        		'fee' 					=> $this->getFee(),
        		'baseFee' 				=> $this->getBaseFee(),
        		'trustFee'				=> $this->getTrustFee(),
        		'defaultEstDuration'	=> $this->getDefaultEstDuration(),
        		'group' 				=> $groupData
        		];
    }

}