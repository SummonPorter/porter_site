<?php

// src/AppBundle/Utilities/Misc/EmailValidator.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Utilities\Misc\RequestValidator;
use AppBundle\Security\SecurityOperations;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class EmailValidator extends RequestValidator {

	//	field where email data can be found
	private $locationKey;

	//========================================================================================
	//	Constructor
	//========================================================================================

	public function __construct(Logger $logger_, SecurityOperations $security_, Request $request_, string $dataMethod_) {
		parent::__construct($logger_, $security_, $request_, $dataMethod_);

		$this->setLocationKey(null);
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function setLocationKey(string $key_ = null) {
		$this->locationKey = $key_;
		if ($this->locationKey === null)
			$this->locationKey = 'null';
	}

	public function getLocationKey() {
		return $this->locationKey;
	}

	//========================================================================================
	//	Runs the validator
	//========================================================================================

	//	uses the input to build a profile which outlines if a city, region, and country were found or not
	public function runValidation(): bool {
		//	if basic validation is false, return
		if (parent::runValidation() === false)
			return false;

		//	if validating the elements discovers an error, return
		if ($this->setDataValidAndMsg($this->validateData(), 'Sorry, your email address appears to be missing. Please make sure the field isn\'t empty.', null) === false)
			return false;

		//	if data doens't appear to be a valid email address, return
		if ($this->setDataValidAndMsg($this->validateFormat(), 'Sorry, the email address you provided wasn\t valid. Please double check your email for errors.', null) === false)
			return false;

		return $this->isDataValid();
	}

	//========================================================================================
	//	Validation
	//========================================================================================

	//	ensures data is valid (exists) and returns true or false depending
	protected function validateData(): bool {
		return $this->validateArrayElement($this->getRequestData(), $this->getLocationKey());
	}

	//	ensures the data looks like an actual email address
	protected function validateFormat(): bool {
		return filter_var($this->getRequestData()[$this->getLocationKey()], FILTER_VALIDATE_EMAIL) !== false;
	}

}