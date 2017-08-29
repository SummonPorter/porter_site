<?php

// src/AppBundle/Services/MandrillOperations.php
namespace AppBundle\Services;

use AppBundle\Entity\MandrillEmailOp;

class MandrillOperations {

	public static $KEY			= 'j-covAcpRHabBxpdKKdXRA';
	public static $USER			= 'PocketPorter Technologies Inc.';
	public static $URL_BASE		= 'https://mandrillapp.com/api/1.0/';

	//	creates a customer record with payfirma. returns true if it was successful
	public function sendEmail(MandrillEmailOp $emailOp_) {
		$url = self::$URL_BASE . '/messages/send.json';

		$post_data = $this->buildEmailJson($emailOp_);
		$post_data = json_encode($post_data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($post_data)]);
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === null || $result === false)
			return false;
		$returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$result = json_decode($result, true);

		return $result;
	}

	private function buildEmailJson(MandrillEmailOp $emailOp_): array {

		$message = [
			'key'			=> self::$KEY,
			'message'		=> [
								'html'			=> $emailOp_->getHtmlBody(),
								'text'			=> $emailOp_->getTextBody(),
								'subject'		=> $emailOp_->getSubject(),
								'from_email'	=> $emailOp_->getFromEmail(),
								'from_name'		=> $emailOp_->getFromName(),
								'to'			=> $emailOp_->getRecipients(),
								'metadata'		=> [
													'website'		=> 'www.summonporter.ca'
												]
							],
			'async'			=> false
		];

		return $message;
	}

}