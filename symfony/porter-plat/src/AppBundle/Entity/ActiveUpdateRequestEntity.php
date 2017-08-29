<?php

// src/AppBundle/Entity/ActiveUpdateRequestEntity.php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CustomersDB;
use AppBundle\Entity\RequestAddressesDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\PersistentCollection;

class ActiveUpdateRequestEntity {

	//========================================================================================
	//	Request Parts
	//========================================================================================

	/**
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $rId;

	private $rDiscount;

	/**
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $rPortersWanted;

	/**
	* @Assert\NotNull()
	*/
	private $rPorterAssignments;

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

	/**
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $rUpdateStartEpoch;

	public function __construct(RequestsDB $request_ = null) {
		$this->setRPorterAssignments([]);
		$this->setRAddresses([]);

		if ($request_ !== null) {
			$this->setRId($request_->getId());
			$this->setRDiscount($request_->getDiscount());
			$this->setRPortersWanted($request_->getPortersWanted());
			$this->setRStartDateTime($request_->getRequestStartDateTime());
			$this->setREstDuration($request_->getEstDuration());
			$this->setRDetails($request_->getDetails());
			$this->setRAddresses($request_->getAddresses());
			$this->setRUpdateStartEpoch(round(microtime(true) * 1000));

			//	loop through assignments, adding the porter ids
			foreach ($request_->getAssignedPorters() as $porter)
				$this->addRPorterAssignments($porter->getId());
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

	public function setRDiscount($rDiscount_) {
		$this->rDiscount = $rDiscount_;
	}

	public function getRDiscount() {
		return $this->rDiscount;
	}
	
	public function getRPorterAssignments() {
		return $this->rPorterAssignments;
	}

	public function setRPorterAssignments($rPorterAssignments_) {
		$this->rPorterAssignments = $rPorterAssignments_;
	}

	public function setRPortersWanted($rPortersWanted_) {
		$this->rPortersWanted = $rPortersWanted_;
	}

	public function getRPortersWanted() {
		return $this->rPortersWanted;
	}

	public function setRAddress($rAddress_) {
		$this->rAddress = $rAddress_;
	}

	public function getRAddress() {
		return $this->rAddress;
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

	public function setRUpdateStartEpoch($updateStartEpoch_) {
		$this->rUpdateStartEpoch = $updateStartEpoch_;
	}

	public function getRUpdateStartEpoch() {
		return $this->rUpdateStartEpoch;
	}

	//========================================================================================
	//	Porter Assignments
	//========================================================================================

	public function addRPorterAssignments($id_) {
        $this->rPorterAssignments[] = $id_;
	}

	public function getRPorterAssignmentsCount() {
		return count($this->getRPorterAssignments());
	}

}