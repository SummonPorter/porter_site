<?php

// src/AppBundle/Entity/CustomerLoginActivityDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\CustomersDB;
use \DateTime;
/**
* @ORM\Entity
* @ORM\Table(name="customerLoginActivity")
*/
class CustomerLoginActivityDB {

	/**
	* @ORM\Id
    * @ORM\ManyToOne(targetEntity="CustomersDB")
    * @ORM\JoinColumn(name="customerId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $customer;

	/**
	* @ORM\Id
	* @ORM\Column(type="bigint", length=14, name="activityEpoch")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $activityEpoch;

	/**
	* @ORM\Column(type="string", length=32, name="activity")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $activity;

	/**
	* @ORM\Column(type="boolean", name="success")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $success;

	/**
	* @ORM\Column(type="string", length=64, name="ip")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $ip;

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getCustomer() {
		return $this->customer;
	}

	public function setCustomer(CustomersDB $customer_) {
		$this->customer = $customer_;
	}

	public function getActivityEpoch() {
		return $this->activityEpoch;
	}

	public function setActivityEpoch($activityEpoch_) {
		$this->activityEpoch = $activityEpoch_;
	}

	public function getActivity() {
		return $this->activity;
	}

	public function setActivity($activity_) {
		$this->activity = $activity_;
	}

	public function getSuccess() {
		return $this->success;
	}

	public function setSuccess($success_) {
		$this->success = $success_;
	}

	public function getIp() {
		return $this->ip;
	}

	public function setIp($ip_) {
		$this->ip = $ip_;
	}

	//========================================================================================
	//	Misc
	//========================================================================================

	public function getActivityEpochString() {
		$date = DateTime::createFromFormat('U', round($this->getActivityEpoch() / 1000));
		return $date->format('Y-m-d H:i:s');
	}

}