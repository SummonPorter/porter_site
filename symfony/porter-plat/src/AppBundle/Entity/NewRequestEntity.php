<?php

// src/AppBundle/Entity/NewRequestEntity.php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CustomersDB;

class NewRequestEntity {

	//========================================================================================
	//	Customer Parts
	//========================================================================================

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $cEmail;

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $cFirstName;

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $cLastName;

	/**
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $cMobilePhone;

	/**
	* @Assert\Type("string")
	*/
	private $cHomePhone;

	private $cCity;

	/**
	* @Assert\Type("string")
	*/
	private $cAddress;

	/**
	* @Assert\Type("string")
	*/
	private $cMailing;

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $ccNum;

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $ccName;

	/**
	* @Assert\NotNull()
	*/
	private $ccExp;

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $ccCode;

	//========================================================================================
	//	Request Parts
	//========================================================================================

	/**
	* @Assert\NotNull()
	*/
	private $rService;

	private $rDiscount;

	/**
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $rPortersWanted;

	/**
	* @Assert\NotNull()
	* @Assert\DateTime()
	*/
	private $rStartDateTime;

	/**
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $rEstDuration;

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $rDetails;

	//========================================================================================
	//	Customer Setters & Getters
	//========================================================================================

	public function setCEmail($cEmail_) {
		$this->cEmail = $cEmail_;
	}

	public function getCEmail() {
		return $this->cEmail;
	}

	public function setCFirstName($cFirstName_) {
		$this->cFirstName = $cFirstName_;
	}

	public function getCFirstName() {
		return $this->cFirstName;
	}

	public function setCLastName($cLastName_) {
		$this->cLastName = $cLastName_;
	}

	public function getCLastName() {
		return $this->cLastName;
	}

	public function setCMobilePhone($cMobilePhone_) {
		$this->cMobilePhone = $cMobilePhone_;
	}

	public function getCMobilePhone() {
		return $this->cMobilePhone;
	}

	public function setCHomePhone($cHomePhone_) {
		$this->cHomePhone = $cHomePhone_;
	}

	public function getCHomePhone() {
		return $this->cHomePhone;
	}

	public function setCCity($cCity_) {
		$this->cCity = $cCity_;
	}

	public function getCCity() {
		return $this->cCity;
	}

	public function setCAddress($cAddress_) {
		$this->cAddress = $cAddress_;
	}

	public function getCAddress() {
		return $this->cAddress;
	}

	public function setCMailing($cMailing_) {
		$this->cMailing = $cMailing_;
	}

	public function getCMailing() {
		return $this->cMailing;
	}

	public function getCcNum() {
		return $this->ccNum;
	}

	public function setCcNum($ccNum_) {
		$this->ccNum = $ccNum_;
	}

	public function getCcName() {
		return $this->ccName;
	}

	public function setCcName($ccName_) {
		$this->ccName = $ccName_;
	}

	public function getCcExp() {
		return $this->ccExp;
	}

	public function setCcExp($ccExp_) {
		$this->ccExp = $ccExp_;
	}

	public function getCcCode() {
		return $this->ccCode;
	}

	public function setCcCode($ccCode_) {
		$this->ccCode = $ccCode_;
	}

	//========================================================================================
	//	Request Setters & Getters
	//========================================================================================

	public function setRService($rService_) {
		$this->rService = $rService_;
	}

	public function getRService() {
		return $this->rService;
	}

	public function setRDiscount($rDiscount_) {
		$this->rDiscount = $rDiscount_;
	}

	public function getRDiscount() {
		return $this->rDiscount;
	}

	public function setRPortersWanted($rPortersWanted_) {
		$this->rPortersWanted = $rPortersWanted_;
	}

	public function getRPortersWanted() {
		return $this->rPortersWanted;
	}
	
	public function setRStartDateTime($rStartDateTime_) {
		$this->rStartDateTime = $rStartDateTime_;
	}

	public function getRStartDateTime() {
		return $this->rStartDateTime;
	}

	public function setREstDuration($rEstDuration_) {
		$this->rEstDuration = $rEstDuration_;
	}

	public function getREstDuration() {
		return $this->rEstDuration;
	}

	public function setRDetails($rDetails_) {
		$this->rDetails = $rDetails_;
	}

	public function getRDetails() {
		return $this->rDetails;
	}

}