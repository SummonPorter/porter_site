<?php

// src/AppBundle/Entity/CreditCard.php
namespace AppBundle\Entity;

class CreditCard {

	private $firstName;
	private $lastName;
	private $number;
	private $expiration;
	private $security;

	public function __construct($firstName_, $lastName_, $number_, $expiration_, $security_) {
		$this->firstName = $firstName_;
		$this->lastName = $lastName_;
		$this->number_ = $number_;
		$this->expiration = $expiration_;
		$this->security = $security_;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function getNumber() {
		return $this->number;
	}

	public function getExpiration() {
		return $this->expiration;
	}

	public function getSecurity() {
		return $this->security;
	}

}