<?php

// src/AppBundle/Entity/PorterRequestActivitySummary.php
namespace AppBundle\Entity;

use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\PorterRequestActivityDB;
use \DateTime;

class PorterRequestActivitySummary {

	private $request;

	private $porter;

	private $startActivity;

	private $endActivity;

	private $requestId;

	public function __construct(RequestsDB $request_ = null, PortersDB $porter_ = null, PorterRequestActivityDB $activityA_ = null, PorterRequestActivityDB $activityB_ = null) {
		$this->setRequest($request_);
		$this->setPorter($porter_);
		$this->setStartActivity($this->findActivity('start', $activityA_, $activityB_));
		$this->setEndActivity($this->findActivity('end', $activityA_, $activityB_));
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getRequest() {
		return $this->request;
	}

	public function setRequest(RequestsDB $request_ = null) {
		$this->request = $request_;
		if ($request_ !== null)
			$this->setRequestId($request_->getId());
	}

	public function getRequestId() {
		return $this->requestId;
	}

	public function setRequestId($requestId_) {
		$this->requestId = $requestId_;
	}

	public function getPorter() {
		return $this->porter;
	}

	public function setPorter(PortersDB $porter_ = null) {
		$this->porter = $porter_;
	}

	public function getStartActivity() {
		return $this->startActivity;
	}

	public function setStartActivity(PorterRequestActivityDB $startActivity_ = null) {
		$this->startActivity = $startActivity_;
	}

	public function getEndActivity() {
		return $this->endActivity;
	}

	public function setEndActivity(PorterRequestActivityDB $endActivity_ = null) {
		$this->endActivity = $endActivity_;
	}

	//========================================================================================
	//	Helpers
	//========================================================================================

	private function findActivity(string $activity_, PorterRequestActivityDB $activityA_ = null, PorterRequestActivityDB $activityB_ = null) {
		if ($activityA_ !== null) {
			if ($activityA_->getActivity() === $activity_)
				return $activityA_;
		}
		if ($activityB_ !== null) {
			if ($activityB_->getActivity() === $activity_)
				return $activityB_;
		}

		//	create a new PorterRequestActivityDB entity object
		$newActivity = new PorterRequestActivityDB();
		$newActivity->setPorter($this->porter);
		$newActivity->setRequest($this->request);
		$newActivity->setActivityDateTime(null);
		$newActivity->setActivity($activity_);
		return $newActivity;
	}

}