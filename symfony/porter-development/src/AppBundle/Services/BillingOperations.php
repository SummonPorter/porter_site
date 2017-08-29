<?php

// src/AppBundle/Services/BillingOperations.php
namespace AppBundle\Services;

use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CitiesDB;
use Symfony\Bridge\Monolog\Logger;
use \DateTime;
use \DateTimeZone;

class BillingOperations {

	public static $PAYFIRMA_MERCHANT_ID 	= '913db9d7b6';
	public static $PAYFIRMA_API_KEY			= '6763dbcbd0879d7df6b95fe26ddf049920bf97c2';
	public static $PAYFIRMA_PUB_ENC_KEY		= '2d2d2d2d2d424547494e205055424c4943204b45592d2d2d2d2d4d494942496a414e42676b71686b6947397730424151454641414f43415138414d49494243674b434151454132354a532f77393066686b79506b726576356431476a3162766d686a314e6f2f3633355a47757636474c7639477939675a58354b527762706b6635676330524e4536316a7665456136537069624e52556a4e4a7267657433596b4a6f6e43545771727435796d36305a485769436c473439475834713264496d324856654c4957465538354154337273426f65587576646376687a685a51613061325a42463457524a376177596831646245724e667736393531593258594349396b6a484667337775553949464f5a70397548703264442b4d4e69515279694979514d4263594c4e6670304650727348523151715a504c5749474e6333636d70724a4e6f636650554d42656154766750656b73495a32567866694f6b5858756e636b71753050356b494a3743622f576c674d6c4c30347a703945744a6f39673559433151376e32426265486f4b75566557433858654b367673733976774944415141422d2d2d2d2d454e44205055424c4943204b45592d2d2d2d2d';

	private $debug;
	private $environment;
	private $logger;

	//	constructor to inject debug value
	public function __construct(string $debug_, string $environment_, Logger $logger_) {
		$this->debug = $debug_;
		$this->environment = $environment_;
		$this->logger = $logger_;
	}

	//	returns results of payfirma call - 'result_bool' indicates if the call was successful or not
	public function validateWildCard(string $firstName_ = null, string $lastName_ = null, $amount_ = null, string $token_ = null) {
		//	safety
		if ($firstName_ === null || $lastName_ === null || $amount_ === null || $token_ === null)
			return false;

		//	if environment is dev, or debug mode is true, set amount to $0.50
		if ($this->environment === 'dev' || $this->debug === true)
			$amount_ = 0.50;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://ecom.payfirma.com/authorize');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);

		$data = [
			'merchant_id'		=> self::$PAYFIRMA_MERCHANT_ID,
			'key'				=> self::$PAYFIRMA_API_KEY,
			'amount'			=> $amount_,
			'token'				=> $token_,
			'first_name'		=> $firstName_,
			'last_name'			=> $lastName_
		];

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);

		if ($output === null || $output === false)
			return false;

		$output = json_decode($output, true);
		//	being extra safe, test to see if 'result_bool' exists in returned data, if it doesn't return false
		if (array_key_exists('result_bool', $output) === false)
			return false;

		return $output;
	}

	//	returns true if there is already a customer record or if one was setup successfully, or false if there were problems
	public function setupCustomerRecord(string $customerEmail_, string $firstName_, string $lastName_, string $token_): bool {
		if ($customerEmail_ === null || $firstName_ === null || $lastName_ === null || $token_ === null)
			return false;

		//	TODO: payfirma supports max of 5 cards, and duplicate cards can be added. must remove cards if records match and replace with new card
		/*$recordExists = $this->customerRecordExists($customerEmail_);
		
		if ($recordExists === true)
			return true;*/
			//	TODO: remove existing cards from customer's record and add new card

		return $this->createCustomerRecord($customerEmail_, $firstName_, $lastName_, $token_);
	}

	//	returns true if a customer record exists, or false if none exist
	public function customerRecordExists(string $customerEmail_ = null): bool {
		$results = $this->getCustomerRecord($customerEmail_);

		if ($results !== false)
			return true;
		return false;
	}

	//	returns false if there is nothing returned or there was an error
	public function getCustomerRecord(string $customerEmail_ = null) {
		if ($customerEmail_ === null)
			return false;

		$url = 'https://ecom.payfirma.com/vault/' . $customerEmail_;

		$post_fields = [
			'key' 			=> self::$PAYFIRMA_API_KEY,
			'merchant_id' 	=> self::$PAYFIRMA_MERCHANT_ID,
			'method' 		=> 'GET' // override POST method
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === null || $result === false)
			return false;

		$result = json_decode($result, true);
		//	if the return contains an error, return false
		if (array_key_exists('error', $result) === true)
			return false;
		return $result;
	}

	//	creates a customer record with payfirma. returns true if it was successful
	public function createCustomerRecord(string $customerEmail_, string $firstName_, string $lastName_, string $token_): bool {
		$url = 'https://ecom.payfirma.com/vault/' . $customerEmail_;

		$post_fields = [
			'key' 			=> self::$PAYFIRMA_API_KEY,
			'merchant_id' 	=> self::$PAYFIRMA_MERCHANT_ID,
			'token' 		=> $token_,
			'first_name' 	=> $firstName_,
			'last_name' 	=> $lastName_
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === null || $result === false)
			return false;

		$result = json_decode($result, true);
		//	if the return contains an error, return false
		return !array_key_exists('error', $result);
	}

	//	bills the customer, returns true on success or false on failure
	public function billCustomer(string $customerEmail_, $amount_): bool {
		//	if environment is dev, or debug mode is true, just return true
		if ($this->environment === 'dev' || $this->debug === true) {
			$this->logger->debug('billing skipped for customer due to debug mode or dev environment');
			return true;
		}

		$url = 'https://ecom.payfirma.com/sale/' . $customerEmail_;

		$post_fields = [
			'key' 			=> self::$PAYFIRMA_API_KEY,
			'merchant_id' 	=> self::$PAYFIRMA_MERCHANT_ID,
			'amount' 		=> $amount_
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === null || $result === false)
			return false;

		$result = json_decode($result, true);

		//	if the return contains an error, return false
		return !array_key_exists('error', $result);
	}

}