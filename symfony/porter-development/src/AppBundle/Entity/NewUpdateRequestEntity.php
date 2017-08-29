<?php

// src/AppBundle/Entity/NewUpdateRequestEntity.php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CustomersDB;
use AppBundle\Entity\RequestAddressesDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\PersistentCollection;

class NewUpdateRequestEntity {

	//========================================================================================
	//	Request Parts
	//========================================================================================

	/**
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $rId;

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

	/**
    * @Assert\NotNull()
    */
    private $rAddresses;

	public function __construct(RequestsDB $request_ = null) {
		$this->setRAddresses([]);

		if ($request_ !== null) {
			$this->setRId($request_->getId());
			$this->setRService($request_->getService());
			$this->setRDiscount($request_->getDiscount());
			$this->setRPortersWanted($request_->getPortersWanted());
			$this->setRStartDateTime($request_->getRequestStartDateTime());
			$this->setREstDuration($request_->getEstDuration());
			$this->setRDetails($request_->getDetails());
			$this->setRAddresses($request_->getAddresses());
		}
	}

	//========================================================================================
	//	Request Setters & Getters
	//========================================================================================

	public function setRId($rId_) {
		$this->rId = $rId_;
	}

	public function getRId() {
		return $this->rId;
	}

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

	public function getRAddresses() {
		if ($this->rAddresses instanceof PersistentCollection)
			return $this->rAddresses->getValues();
		if ($this->rAddresses instanceof ArrayCollection)
			return $this->rAddresses->getValues();
		return $this->rAddresses;
	}

	public function setRAddresses($rAddresses_) {
		$this->rAddresses = $rAddresses_;
	}

}