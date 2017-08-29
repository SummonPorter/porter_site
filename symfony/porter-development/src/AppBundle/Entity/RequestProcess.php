<?php

// src/AppBundle/Entity/RequestProcess.php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\RequestsDB;

class RequestProcess {

	/**
	* @Assert\NotBlank()
	* @Assert\Type("integer")
	*/
	protected $requestId;

	/**
	* @Assert\NotBlank()
	* @Assert\Type("string")
	*/
	protected $newState;

	protected $submitText;

	public function __construct(RequestsDB $request_ = null, string $newState_ = '', string $submitText_ = '') {
		if ($request_ !== null)
			$this->setRequestId($request_->getId());
		$this->setNewState($newState_);
		$this->setSubmitText($submitText_);
	}

	public function getRequestId() {
		return $this->requestId;
	}

	public function setRequestId($requestId_) {
		$this->requestId = $requestId_;
	}

	public function getNewState() {
		return $this->newState;
	}

	public function setNewState($newState_) {
		$this->newState = $newState_;
	}

	public function getSubmitText() {
		return $this->submitText;
	}

	public function setSubmitText($submitText_) {
		$this->submitText = $submitText_;
	}

}