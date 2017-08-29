<?php

// src/AppBundle/Services/MailchimpOperations.php
namespace AppBundle\Services;

use \DateTime;
use \DateTimeZone;

class MailchimpOperations {

	public static $MAILCHIMP_API_KEY		= 'dd29faa2b7dff50d914d8b3c6c4f6b2d-us13';
	public static $SUBSCRIBERS_LIST_ID		= '6a19fc3109';
	public static $SERVER_DC				= 'us13';

	private function getServer(): string {
		return 'https://' . self::$SERVER_DC . '.api.mailchimp.com/3.0';
	}

	private function getSubscribeSlug(string $listId_, string $memberId_): string {
		return '/lists/' . self::$SUBSCRIBERS_LIST_ID . '/members/' . $memberId_;
	}

	private function getAuthenticCurl(string $slug_) {
		$curl = curl_init();

		//	get base url and combine with slug
		$url = $this->getServer() . $slug_;

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERPWD, 'user:' . self::$MAILCHIMP_API_KEY);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
		curl_setopt($curl, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		return $curl;
	}

	//	adds or updates an email to the subscribers list
	public function subscribeEmail(string $email_) {
		//	build member id
		$memberId = md5(strtolower($email_));
		//	get curl resource with the basics setup for a subscribe call
		$curl = $this->getAuthenticCurl($this->getSubscribeSlug(self::$SUBSCRIBERS_LIST_ID, $memberId));
		
		//	set the data
		$data = [
			'email_address' => $email_,
			'status'        => 'subscribed'
		];
		
		//	configure the rest of curl
		//curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

		//	make the call
		$result = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $httpCode;
	}

}