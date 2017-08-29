<?php

// src/AppBundle/Utilities/Misc/NewCustomerProcessor.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Entity\CustomersDB;
use AppBundle\Utilities\Misc\RequestActor;
use AppBundle\Utilities\Misc\RequestValidator;
use AppBundle\Utilities\Misc\LocationValidator;
use AppBundle\Utilities\Misc\EmailValidator;
use AppBundle\Security\SecurityOperations;
use AppBundle\Services\Database;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use \JsonSerializable;

class NewCustomerProcessor extends RequestValidator implements RequestActor {

	//	services
	private $doctrine;
	private $database;
	
	//	whether or not there was a conflict with the provided email
	private $emailConflict;
	//	a EmailValidator object that ensures the porter's email is valid
	private $emailValidator;

	//========================================================================================
	//	Constructor
	//========================================================================================

	public function __construct(Logger $logger_, SecurityOperations $security_, Request $request_, string $dataMethod_) {
		parent::__construct($logger_, $security_, $request_, $dataMethod_);

		$this->doctrine 			= null;
		$this->database				= null;
		$this->emailConflict		= false;
		$this->emailValidator		= new EmailValidator($logger_, $security_, $request_, $dataMethod_);
		$this->emailValidator->setLocationKey('email');
		$this->registerSubValidator($this->emailValidator);
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function setDoctrine(Registry $doctrine_) {
		$this->doctrine = $doctrine_;
	}

	public function setDatabase(Database $database_) {
		$this->database = $database_;
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

		//	if validating the elements discovers an error, return
		if ($this->setDataValidAndMsg($this->isEmailUnique(), 'Sorry it appears that the email you provided is already registered.', null) === false)
			return false;

		//	confirming that the 2 passwords match
		if ($this->setDataValidAndMsg($this->passwordsMatch(), 'Sorry but the passwords do not match.', null) === false)
			return false;

		//	validating that the passwords are long enough and random enough
		if ($this->setDataValidAndMsg($this->passwordsMatch(), 'Sorry but your password is not long enough.', null) === false)
			return false;

		return $this->isDataValid();
	}

	//========================================================================================
	//	Validation
	//========================================================================================

	//	ensures data is valid (exists) and returns true or false depending
	protected function validateData(): bool {
		$valid = true;
		$valid &= $this->validateArrayElement($this->getRequestData(), 'firstname');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'lastname');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'password');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'password2');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'email');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'mobile');

		return $valid;
	}

	//	ensures the email isn't already in use, returns true if unique
	protected function isEmailUnique(): bool {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CustomersDB', 'c')
            ->where('c.email = ?1')
            ->andWhere('c.guest = false')
            ->setParameter(1, $this->getRequestData()['email']);

        return $qb->getQuery()->getOneOrNullResult() === null;
	}

	//	ensures passwords match
	protected function passwordsMatch(): bool {
		return $this->getRequestData()['password'] === $this->getRequestData()['password2'];
	}

	//	ensures passwords are strong, returns false if the password is too weak
	protected function isPasswordStrong(): bool {
		return strlen($this->getRequestData()['password']) > 7;
	}

	//========================================================================================
	//	Action
	//========================================================================================

	//	put together a customer profile using the submitted data
	public function performAction() {
		if ($this->isDataValid() === true) {
			//	basic customer elements
			$hashPass = $this->getSecurity()->hashCustomerPassword($this->getRequestData()['password']);
			$customer = new CustomersDB();
			$customer->setGuest(false);
			$customer->setFirstName($this->getRequestData()['firstname']);
			$customer->setLastName($this->getRequestData()['lastname']);
			$customer->setMobilePhone($this->getRequestData()['mobile']);
			$customer->setEmail($this->getRequestData()['email']);
			$customer->setPassword($hashPass);
			$customer->setPermissionsGroup('ROLE_CUSTOMER');
			$customer->setSignupEpoch(time());
			$customer->setEnabled(true);

			//	passing the new customer off to SecurityOperations to create the new customer
			$this->setDataValidAndMsg($this->getSecurity()->registerCustomer($customer), 'Sorry it appears that the email you provided is already registered.', null);
		}
	}

}