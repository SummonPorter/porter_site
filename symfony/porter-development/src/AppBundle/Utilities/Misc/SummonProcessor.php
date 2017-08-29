<?php

// src/AppBundle/Utilities/Misc/SummonProcessor.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Entity\CitiesDB;
use AppBundle\Entity\RegionsDB;
use AppBundle\Entity\CountriesDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CustomersDB;
use AppBundle\Entity\RequestAddressesDB;
use AppBundle\Entity\MandrillEmailOp;
use AppBundle\Entity\InternalUsersDB;
use AppBundle\Services\Database;
use AppBundle\Services\BillingOperations;
use AppBundle\Services\RequestOperations;
use AppBundle\Services\MandrillOperations;
use AppBundle\Utilities\Misc\RequestActor;
use AppBundle\Utilities\Misc\RequestValidator;
use AppBundle\Utilities\Misc\LocationValidator;
use AppBundle\Utilities\Misc\EmailValidator;
use AppBundle\Security\SecurityOperations;
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
use \DateTime;
use \DateTimeZone;

class SummonProcessor extends RequestValidator implements RequestActor {

	//	services
	private $doctrine;
	private $database;
	private $billing;
	private $requestOps;
	private $mandrillOps;
	//	summon service
	private $service;
	//	discount (if applicable, may be null)
	private $discount;
	//	datetime object
	private $startDateTime;
	//	authorization transaction id
	private $authTransId;
	//	location validators
	private $locationValidators;
	//	location data
	private $locationData;
	//	email validator
	private $emailValidator;

	//========================================================================================
	//	Constructor
	//========================================================================================

	public function __construct(Logger $logger_, SecurityOperations $security_, Request $request_, string $dataMethod_) {
		parent::__construct($logger_, $security_, $request_, $dataMethod_);

		$this->doctrine 		= null;
		$this->database 		= null;
		$this->billing 			= null;
		$this->requestOps 		= null;

		$this->service 			= null;
		$this->discount 		= null;
		$this->startDateTime 	= null;
		$this->authTransId		= null;

		$this->locationValidators = [];
		$this->locationData = [];

		$this->emailValidator = new EmailValidator($logger_, $security_, $request_, $dataMethod_);
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

	public function setBilling(BillingOperations $billing_) {
		$this->billing = $billing_;
	}

	public function setRequestOps(RequestOperations $requestOps_) {
		$this->requestOps = $requestOps_;
	}

	public function setMandrillOps(MandrillOperations $mandrillOps_) {
		$this->mandrillOps = $mandrillOps_;
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

		//	if running validation on the location validator(s) returns an error, return
		if ($this->setDataValidAndMsg($this->runSubValidators(), 'Sorry, there was a problem with the address information you provided.', null) === false)
			return false;

		//	creating the start date time for the request
		if (empty($this->locationValidators) === false) {
			$startCity = $this->locationValidators[0]->getCity();
			$timezone = $startCity->getTimezone();

			if ($timezone !== null) {
				$objTimeZone = new DateTimeZone($timezone);
				$this->startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $this->getRequestData()['datetime'], $objTimeZone);
			}
			else
				$this->startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $this->getRequestData()['datetime'], null);
		}

		//	if validating the discount encounters an error, return false
		if ($this->setDataValidAndMsg($this->validateDiscount(), 'Sorry, the discount code you gave was either expired or invalid.', null) === false)
			return false;

		//	if running validation on the credit card details and attempt to build customer record
		if ($this->setDataValidAndMsg($this->validateCreditCard(), 'Sorry, there was a problem authorizing your card. Please double check your details.', null) === false)
			return false;

		return $this->isDataValid();
	}	

	//========================================================================================
	//	Validate data
	//========================================================================================

	private function validateData(): bool {
		$valid = true;
		$valid &= $this->validateArrayElement($this->getRequestData(), 'token');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'summon_description');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'first_name');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'last_name');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'mobile');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'email');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'card_holder_first_name');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'card_holder_last_name');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'service_id');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'currency');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'est_total');
		$valid &= $this->validateArrayElement($this->getRequestData(), 'datetime');

		if ($valid === false)
			return false;

		//	loading the service, checking for number of addresses
		$this->service = $this->database->getService($this->getRequestData()['service_id']);
		$valid &= $this->service !== null;

		if ($this->service !== null) {
			
			if ($this->service->getAddressCount() === 1) {
				$this->locationData[] = ['address' => $this->getRequestData()['pickup_address'], 'unit' => $this->getRequestData()['pickup_address_apt'], 'context' => null, 'stepping' => 0];
				$this->locationValidators[] = new LocationValidator($this->getLogger(), $this->getSecurity(), $this->getRequest(), $this->getDataMethod());
				$this->locationValidators[0]->setDoctrine($this->doctrine);
				$this->locationValidators[0]->setLocationKey('pickup_address');
				$this->registerSubValidator($this->locationValidators[0]);
			}
			elseif ($this->service->getAddressCount() === 2) {
				$this->locationData[] = ['address' => $this->getRequestData()['pickup_address'], 'unit' => $this->getRequestData()['pickup_address_apt'], 'context' => 'Pickup', 'stepping' => 0];
				$this->locationValidators[] = new LocationValidator($this->getLogger(), $this->getSecurity(), $this->getRequest(), $this->getDataMethod());
				$this->locationValidators[0]->setDoctrine($this->doctrine);
				$this->locationValidators[0]->setLocationKey('pickup_address');
				$this->registerSubValidator($this->locationValidators[0]);

				$this->locationData[] = ['address' => $this->getRequestData()['deliver_address'], 'unit' => $this->getRequestData()['deliver_address_apt'], 'context' => 'Delivery', 'stepping' => 1];
				$this->locationValidators[] = new LocationValidator($this->getLogger(), $this->getSecurity(), $this->getRequest(), $this->getDataMethod());
				$this->locationValidators[1]->setDoctrine($this->doctrine);
				$this->locationValidators[1]->setLocationKey('deliver_address');
				$this->registerSubValidator($this->locationValidators[1]);
			}
			else
				return false;
		}

		return $valid;
	}

	//	TODO: replace with Discount Validator
	protected function validateDiscount(): bool {
		//	if there is a discount code
		if ($this->validateArrayElement($this->getRequestData(), 'discount_code')) {
			$em = $this->doctrine->getManager();
	        $qb = $em->createQueryBuilder();
	        $qb->select('d')
	            ->from('AppBundle:DiscountsDB', 'd')
	            ->where('LOWER(d.code) = LOWER(?1)')
	            ->setParameter(1, $this->getRequestData()['discount_code']);

	        $this->discount = $qb->getQuery()->getOneOrNullResult();

	        //	validating the discount is applicable
	        if ($this->discount !== null) {
	        	if ($this->discount->hasWhitelistService($this->service) === false)
	        		return false;

	        	$cTime = time();
	        	if ($cTime < $this->discount->getStartEpoch() || $cTime > $this->discount->getEndEpoch())
	        		return false;

	        	return true;
	        }

	        return false;
		}

		return true;
	}

	protected function validateCreditCard(): bool {
		//	test the card to see if it is authorized
		$firmaResponse = $this->billing->validateWildCard($this->getRequestData()['card_holder_first_name'], $this->getRequestData()['card_holder_last_name'], $this->getRequestData()['est_total'], $this->getRequestData()['token']);
		//	error during call
		if ($firmaResponse === false)
			return false;
		//	if payfirma reported an error
		if ($firmaResponse['result_bool'] === false)
			return false;

		//	grab the authorization transaction id for analytics
		$this->authTransId = $firmaResponse['transaction_id'];

		//	try and create a customer record in the customer vault, if needed. Returns true if record was created or if record already exists
		$firmaResponse = $this->billing->setupCustomerRecord($this->getRequestData()['email'], $this->getRequestData()['first_name'], $this->getRequestData()['last_name'], $this->getRequestData()['token']);
		//	could not create customer record
		if ($firmaResponse === false)
			return false;

		return true;
	}

	//========================================================================================
	//	Action
	//========================================================================================

	//	put together a porter profile using the submitted data
	public function performAction() {
		if ($this->isDataValid()) {
			//	building Customer
			$customer = new CustomersDB();
			$customer->setGuest(true);
			$customer->setEmail($this->getRequestData()['email']);
			$customer->setFirstName($this->getRequestData()['first_name']);
			$customer->setLastName($this->getRequestData()['last_name']);
			$customer->setMobilePhone($this->getRequestData()['mobile']);
			
			//	building addresses
			$addresses = [];
			for ($i = 0; $i < count($this->locationValidators); $i++) {
				$validator 	= $this->locationValidators[$i];
				$locInfo	= $this->locationData[$i];

				$nAddress 	= new RequestAddressesDB();
				$nAddress->setCity($validator->getCity());
				$nAddress->setAddress($locInfo['address']);
				$nAddress->setUnit($locInfo['unit']);
				$nAddress->setContext($locInfo['context']);
				$nAddress->setStepping($locInfo['stepping']);
				$addresses[] = $nAddress;
			}

			//	build request
			$nRequest = $this->requestOps->buildNewRequest($customer, $this->service, 1, $this->startDateTime, 30, $this->getRequestData()['summon_description'], $addresses, $this->discount);

			//	update database
			$this->database->addEntities([$nRequest], false, true);

			//	send out emails to admins alerting there is a new summon
			$this->notifyAdmins($nRequest, $customer);

			//	send out email to customer notifying them that their summon has been recieved
			$this->notifyCustomer($nRequest, $customer);
		}
	}

	private function notifyAdmins(RequestsDB $request_, CustomersDB $customer_) {
		$from       = "info@summonporter.ca";
        $fName      = "Summon Porter Info";
        $subject    = 'New Summon (' . $request_->getService()->getName() . ') Received!';
        $hMsg       = 'customer: '  . $customer_->getFirstName() . ' ' . $customer_->getLastName() . ' ' . $customer_->getEmail() . ' ' . $customer_->getMobilePhone();
        $tMsg       = 'customer: '  . $customer_->getFirstName() . ' ' . $customer_->getLastName() . ' ' . $customer_->getEmail() . ' ' . $customer_->getMobilePhone();

        $mandrillEmail = new MandrillEmailOp($from, $fName, $subject, $hMsg, $tMsg);
        
        //	TODO: add proper support for different internal users :{}
        //	get all internal users, and add them as recipients
        $internalUsers = $this->database->getInternalUsers();
        foreach ($internalUsers as $user)
        	$mandrillEmail->addRecipient($user->getEmail());

        //	send emails
        $this->mandrillOps->sendEmail($mandrillEmail);
	}

	private function notifyCustomer(RequestsDB $request_, CustomersDB $customer_) {
		$from       = "info@summonporter.ca";
        $fName      = "Summon Porter Info";
        $subject    = 'Porter ' . $request_->getService()->getName() . ' Summon';
        $hMsg       = 'Hello '  . $customer_->getFirstName() . ',<br><br>';
        $hMsg	   .= 'Thank you for choosing Porter for your ' . $request_->getService()->getName() . ' request. We\'re currently matching you with a Porter to fulfill this summon for you.<br><br>';
        $hMsg	   .= 'Summon Summary<br><br>Date: ' . $request_->getRequestStartDateTime()->format('Y-m-d H:i:s') . '<br><br>Description: ' . $request_->getDetails() . '<br><br><br>';
        $hMsg	   .= 'Thank you,<br><br>Porter Community Team';
        $tMsg       = 'Hello '  . $customer_->getFirstName() . ',\r\n';
        $tMsg	   .= 'Thank you for choosing Porter for your ' . $request_->getService()->getName() . ' request. We\'re currently matching you with a Porter to fulfill this summon for you.\r\n';
        $tMsg	   .= 'Summon Summary\r\nDate: ' . $request_->getRequestStartDateTime()->format('Y-m-d H:i:s') . '\r\nDescription: ' . $request_->getDetails() . '\r\n';
        $tMsg	   .= 'Thank you,\r\nPorter Community Team';
        
        $mandrillEmail = new MandrillEmailOp($from, $fName, $subject, $hMsg, $tMsg);

        //	add customer as recipient
        $mandrillEmail->addRecipient($customer_->getEmail());

        //	send emails
        $this->mandrillOps->sendEmail($mandrillEmail);
	}

	//========================================================================================
	//	JsonSerializable
	//========================================================================================

	public function jsonSerialize() {
		$returns = parent::jsonSerialize();
		$returns['transactionId'] = $this->authTransId;

		return $returns;
    }

}