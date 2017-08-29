<?php

// src/AppBundle/Utilities/Misc/NewPorterProcessor.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\ServiceGroupsDB;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServiceInterestGradeDB;
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

class NewPorterProcessor extends RequestValidator implements RequestActor {

	//	services
	private $doctrine;
	private $database;
	
	//	whether or not there was a conflict with the provided email
	private $emailConflict;
	//	a LocationValidator object that ensures the porter is from a valid location
	private $locationValidator;
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
		$this->locationValidator	= new LocationValidator($logger_, $security_, $request_, $dataMethod_);
		$this->locationValidator->setLocationKey('city');
		$this->registerSubValidator($this->locationValidator);
		$this->emailValidator		= new EmailValidator($logger_, $security_, $request_, $dataMethod_);
		$this->emailValidator->setLocationKey('email');
		$this->registerSubValidator($this->emailValidator);
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function setDoctrine(Registry $doctrine_) {
		$this->doctrine = $doctrine_;
		$this->locationValidator->setDoctrine($doctrine_);
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

		return $this->isDataValid();
	}

	//========================================================================================
	//	Validation
	//========================================================================================

	//	ensures data is valid (exists) and returns true or false depending
	protected function validateData(): bool {
		$valid = true;
		$valid &= $this->validateArrayElement($this->getRequestData(), 'first_name');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'last_name');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'email');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'phone');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'city');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'handyman');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'driver');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'cleaning');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'moving');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'corporate');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'shopping');

		return $valid;
	}

	//	ensures the email isn't already in use, returns true if unique
	protected function isEmailUnique(): bool {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:PortersDB', 'p')
            ->where('p.email = ?1')
            ->setParameter(1, $this->getRequestData()['email']);

        return $qb->getQuery()->getOneOrNullResult() === null;
	}

	//========================================================================================
	//	Action
	//========================================================================================

	//	put together a porter profile using the submitted data
	public function performAction() {
		if ($this->isDataValid() === true) {
			//	basic porter elements
			$porter = new PortersDB();
			$porter->setIdToken($this->getSecurity()->getPorterToken());
			$porter->setFirstName($this->getRequestData()['first_name']);
			$porter->setLastName($this->getRequestData()['last_name']);
			$porter->setMobilePhone($this->getRequestData()['phone']);
			$porter->setEmail($this->getRequestData()['email']);
			$porter->setSignupEpoch(time());
			$porter->setActive(false);
			$porter->setCity($this->locationValidator->getCity());

			//	building out the service interests
			$services = $this->database->getServices(false);
			foreach ($services as $service) {
				$newSG = new ServiceInterestGradeDB();
				$newSG->setService($service);
				$newSG->setInterestGrade($this->calculateInterest($service->getGroup(), $service));
				$porter->addServiceInterestGrade($newSG);
			}

			//	creating the porter
			$this->database->addEntities([$porter], false, true);
		}
	}

	//========================================================================================
	//	Helpers
	//========================================================================================

	//	takes in the service group & service, and returns the interest level for that specific service
	//	HACK: in the future this shouldn't be hardcoded the way it is now - front end form should
	//	reflect the actual backend data
	protected function calculateInterest(ServiceGroupsDB $group_ = null, ServicesDB $service_ = null): int {
		if ($group_ === null || $service_ === null)
			return 2;
		switch (strtolower($group_->getName())) {
			case 'shopping & delivery':
				if ($this->getRequestData()['shopping'] === 'yes') {
					if (strtolower($service_->getName()) === 'grocery shopping')
						return 3;
					if (strtolower($service_->getName()) === 'retail shopping')
						return 3;
					if (strtolower($service_->getName()) === 'wait services')
						return 3;
					if (strtolower($service_->getName()) === 'special request')
						return 3;
				}
				if ($this->getRequestData()['driver'] === 'yes') {
					if (strtolower($service_->getName()) === 'pickup & delivery')
						return 3;
					if (strtolower($service_->getName()) === 'run errands')
						return 3;
					if (strtolower($service_->getName()) === 'special request')
						return 3;
				}
				break;
			case 'cleaning':
				if ($this->getRequestData()['cleaning'] === 'yes')
					return 3;
				break;
			case 'handyman':
				if ($this->getRequestData()['handyman'] === 'yes')
					return 3;
				break;
			case 'moving':
				if ($this->getRequestData()['moving'] === 'yes')
					return 3;
				break;
			case 'corporate':
				if ($this->getRequestData()['corporate'] === 'yes')
					return 3;
				break;
		}

		return 0;
	}

}