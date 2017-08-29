<?php

// src/AppBundle/Utilities/Misc/RequestValidator.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Security\SecurityOperations;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use \JsonSerializable;

class RequestValidator implements JsonSerializable {

	//	logger service
	private $logger;
	//	security service
	private $security;
	//	original request
	private $request;
	//	POST or GET method
	private $dataMethod;

	//	array of sub RequestValidator objects that need to run validations
	private $subValidators;

	//	data pulled from the request and stripped of tags
	private $requestData;
	//	final result, if input was valid or not
	private $dataValid;
	//	return messages for front end
	private $returnMessages;

	//========================================================================================
	//	Constructor
	//========================================================================================

	public function __construct(Logger $logger_, SecurityOperations $security_, Request $request_, string $dataMethod_) {
		$this->logger 			= $logger_;
		$this->security 		= $security_;
		$this->request 			= $request_;
		$this->dataMethod		= $dataMethod_;

		$this->subValidators 	= [];

		$this->requestData	= null;
		if ($this->dataMethod === 'POST')
			$this->requestData 	= $this->request->request->all();
		elseif ($this->dataMethod === 'GET')
			$this->requestData 	= $this->request->query->all();
		if ($this->requestData !== null)
			$this->requestData 	= $this->security->stripTagsFromArray($this->requestData);
		
		$this->dataValid 		= true;
		$this->returnMessages 	= [];
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	protected function getLogger() {
		return $this->logger;
	}

	protected function getSecurity() {
		return $this->security;
	}

	protected function getRequest() {
		return $this->request;
	}

	protected function getDataMethod() {
		return $this->dataMethod;
	}

	protected function getRequestData() {
		return $this->requestData;
	}

	private function getSubValidators(): array {
		return $this->subValidators;
	}

	protected function registerSubValidator(RequestValidator $validator_) {
		$this->subValidators[] = $validator_;
	}

	public function isDataValid(): bool {
		return $this->dataValid;
	}

	protected function setDataValid(bool $valid_) {
		$this->dataValid = $valid_;
	}

	public function getReturnMessages(): array {
		return $this->returnMessages;
	}

	protected function addReturnMessage(string $message_) {
		$this->returnMessages[] = $message_;
	}

	//========================================================================================
	//	Runs the validator
	//========================================================================================

	//	call it once to do everything that needs doing, returns the final state of whether or not data is valid
	public function runValidation(): bool {
		//	performing basic validation that requestData is isn't empty or null
		if ($this->setDataValidAndMsg(!($this->requestData === null || empty($this->requestData) === true), 'Sorry, but nothing appears to have been sent. Please check your information and try again.', null) === false)
			return false;

		//	running sub validator objects
		return $this->runSubValidators();
	}

	//========================================================================================
	//	Helper
	//========================================================================================

	//	returns true if dataValid is valid, returns false otherwise
	//	provided strings are added to returnMessages depending upon whether or not the result of dataValid is true or false
	protected function setDataValidAndMsg(bool $valid_, string $errorMsg_ = null, string $successMsg_ = null): bool {
		$this->setDataValid($valid_);

		if ($errorMsg_ !== null && empty($errorMsg_) === false && $this->isDataValid() === false)
			$this->addReturnMessage($errorMsg_);
		elseif ($successMsg_ !== null && empty($successMsg_) === false && $this->isDataValid() === true)
			$this->addReturnMessage($successMsg_);

		return $this->isDataValid();
	}

	//	returns false if the key doesn't exist, or if the key exists but the value is null or an empty string
	protected function validateArrayElement(array $array_, $key_): bool {
		if (array_key_exists($key_, $array_) === true) {
			$value = $array_[$key_];
			if ($value === null)
				return false;
			if (is_string($value) === true) {
				if (empty($value) === true)
					return false;
			}
			return true;
		}
		return false;
	}

	//	automatically runs any sub RequestValidator objects and returns true or false depending upon those results
	//	also automatically appends any return messages
	protected function runSubValidators(): bool {
		foreach ($this->getSubValidators() as $validator) {
			$validator->runValidation();
			if ($validator->isDataValid() === false) {
				$this->setDataValid(false);
				$this->appendReturnMessages($validator->getReturnMessages());
				return false;
			}
		}

		return true;
	}

	//	appends messages to returnMessages
	protected function appendReturnMessages(array $messages_) {
		foreach ($messages_ as $message)
			$this->addReturnMessage($message);
	}

	//========================================================================================
	//	JSON
	//========================================================================================

	public function getSerialized(): string {
		$encoders = array(new JsonEncoder());
		$normalizers = array(new JsonSerializableNormalizer());

		$serializer = new Serializer($normalizers, $encoders);
		$json = $serializer->serialize($this, 'json');

		return $json;
	}

	//========================================================================================
	//	JsonSerializable
	//========================================================================================

	public function jsonSerialize() {

        return [
        		'inputDeclaredValid' 	=> $this->dataValid,
        		'returnMessages' 		=> $this->returnMessages
		];
    }

}