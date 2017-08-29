<?php

// src/AppBundle/Utilities/Misc/DiscountValidator.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Entity\DiscountsDB;
use AppBundle\Utilities\Misc\RequestValidator;
use AppBundle\Security\SecurityOperations;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class DiscountValidator extends RequestValidator {

	//	services
	private $doctrine;
	//	field where discount code can be found
	private $discountKey;
	//	field where service id can be found
	private $serviceKey;
	//	the discount object
	private $discount;

	//========================================================================================
	//	Constructor
	//========================================================================================

	public function __construct(Logger $logger_, SecurityOperations $security_, Request $request_, string $dataMethod_) {
		parent::__construct($logger_, $security_, $request_, $dataMethod_);

		$this->doctrine = null;
		$this->discount = null;

		$this->setDiscountKey(null);
		$this->setServiceKey(null);
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function setDoctrine(Registry $doctrine_) {
		$this->doctrine = $doctrine_;
	}

	public function setDiscountKey(string $key_ = null) {
		$this->discountKey = $key_;
		if ($this->discountKey === null)
			$this->discountKey = 'null';
	}

	public function getDiscountKey() {
		return $this->discountKey;
	}

	public function setServiceKey(string $key_ = null) {
		$this->serviceKey = $key_;
		if ($this->serviceKey === null)
			$this->serviceKey = 'null';
	}

	public function getServiceKey() {
		return $this->serviceKey;
	}

	public function getDiscount() {
		return $this->discount;
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
		if ($this->setDataValidAndMsg($this->validateData(), 'Sorry, the discount code appears to be missing. Please make sure the field isn\'t empty.', null) === false)
			return false;

		//	if data doens't appear to be a valid email address, return
		if ($this->setDataValidAndMsg($this->validateCode(), 'Sorry, the discount code you\'ve provided isn\'t valid or has expired.', null) === false)
			return false;

		return $this->isDataValid();
	}

	//========================================================================================
	//	Validation
	//========================================================================================

	//	ensures data is valid (exists) and returns true or false depending
	protected function validateData(): bool {
		$valid = true;
		$valid &= $this->validateArrayElement($this->getRequestData(), $this->getDiscountKey());
		$valid &= $this->validateArrayElement($this->getRequestData(), $this->getServiceKey());

		return $valid;
	}

	//	validates the actual discount
	protected function validateCode(): bool {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('d')
            ->from('AppBundle:DiscountsDB', 'd')
            ->where('LOWER(d.code) = LOWER(?1)')
            ->setParameter(1, $this->getRequestData()[$this->getDiscountKey()]);

        $this->discount = $qb->getQuery()->getOneOrNullResult();

        //	validating the discount is applicable
        if ($this->discount !== null) {
        	if ($this->discount->hasWhitelistServiceId($this->getRequestData()[$this->getServiceKey()]) === false)
        		return false;

        	$cTime = time();
        	if ($cTime < $this->discount->getStartEpoch() || $cTime > $this->discount->getEndEpoch())
        		return false;

        	return true;
        }

        return false;
	}

	//========================================================================================
	//	JsonSerializable
	//========================================================================================

	public function jsonSerialize() {
		$returns = parent::jsonSerialize();

		if ($this->discount !== null) {
			$returns['code'] 	= $this->discount->getCode();
			$returns['type'] 	= $this->discount->getDiscountType();
			$returns['amount'] 	= $this->discount->getDiscount();
		}

		return $returns;
    }

}