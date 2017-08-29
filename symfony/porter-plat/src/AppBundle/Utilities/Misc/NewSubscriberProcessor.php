<?php

// src/AppBundle/Utilities/Misc/NewSubscriberProcessor.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Utilities\Misc\RequestActor;
use AppBundle\Utilities\Misc\RequestValidator;
use AppBundle\Utilities\Misc\EmailValidator;
use AppBundle\Security\SecurityOperations;
use AppBundle\Services\MailchimpOperations;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use \JsonSerializable;

class NewSubscriberProcessor extends RequestValidator implements RequestActor {

	//	mailchimp service
	private $mailchimpOps;
	//	email validator
	private $emailValidator;

	//========================================================================================
	//	Constructor
	//========================================================================================

	public function __construct(Logger $logger_, SecurityOperations $security_, Request $request_, string $dataMethod_) {
		parent::__construct($logger_, $security_, $request_, $dataMethod_);

		$this->mailchimpOps = null;

		$this->emailValidator = new EmailValidator($logger_, $security_, $request_, $dataMethod_);
		$this->emailValidator->setLocationKey('email');
		$this->registerSubValidator($this->emailValidator);
	}

	//========================================================================================
	//	Setters
	//========================================================================================

	public function setMailchimpOps(MailchimpOperations $mailchimpOps_) {
		$this->mailchimpOps = $mailchimpOps_;
	}

	//========================================================================================
	//	Runs the validator
	//========================================================================================

	//	uses the input to validate location and the rest of the porter's profile
	public function runValidation(): bool {
		//	if basic validation is false, return
		if (parent::runValidation() === false)
			return false;

		//	if validating the elements discovers an error, return
		if ($this->setDataValidAndMsg($this->validateData(), 'Sorry, information appears to be missing. Please check your information and try again.', null) === false)
			return false;

		return $this->isDataValid();
	}

	//========================================================================================
	//	Validation
	//========================================================================================

	//	ensures data is valid (exists) and returns true or false depending
	protected function validateData(): bool {
		return $this->validateArrayElement($this->getRequestData(), 'email');
	}

	//========================================================================================
	//	Action
	//========================================================================================

	//	put together a porter profile using the submitted data
	public function performAction() {
		//	subscribing the email
		if ($this->isDataValid() === true)
			$this->setDataValidAndMsg($this->mailchimpOps->subscribeEmail($this->getRequestData()['email']) === 200, 'Sorry, there was an internal server error. Please try again.', null);
	}

}