<?php

// src/AppBundle/Entity/WorkedUpdateRequestEntity.php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PorterRequestActivitySummary;

class WorkedUpdateRequestEntity {

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
	*/
	private $porterSummaries;

	public function __construct(RequestsDB $request_ = null) {
		$this->setPorterSummaries([]);

		if ($request_ !== null) {
			$this->setRId($request_->getId());
			$this->setRDiscount($request_->getDiscount());
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
	
	public function getPorterSummaries() {
		return $this->porterSummaries;
	}

	public function setPorterSummaries($porterSummaries_) {
		$this->porterSummaries = $porterSummaries_;
	}

	//========================================================================================
	//	Porter Assignments
	//========================================================================================

	public function addPorterSummary(PorterRequestActivitySummary $porterSummary_) {
        $this->porterSummaries[] = $porterSummary_;
	}

	public function getPorterSummariesCount() {
		return count($this->getPorterSummaries());
	}

}