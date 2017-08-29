<?php

// src/AppBundle/Entity/ServiceInterestGradeDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServicesDB;

/**
* @ORM\Entity
* @ORM\Table(name="porterServiceInterestGrades")
*/
class ServiceInterestGradeDB {

	/**
	* @ORM\Id
    * @ORM\ManyToOne(targetEntity="PortersDB", inversedBy="serviceInterestGrades")
    * @ORM\JoinColumn(name="porterId", referencedColumnName="id")
	*/
	private $porter;

	/**
	* @ORM\Id
    * @ORM\ManyToOne(targetEntity="ServicesDB")
    * @ORM\JoinColumn(name="serviceId", referencedColumnName="id")
	*/
	private $service;

	/**
    * @ORM\Column(type="smallint", length=5, name="interestGrade")
	*
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $interestGrade;

	//	used for form processing
	private $porterId;
	private $serviceId;

	public function getPorter() {
		return $this->porter;
	}

	public function setPorter(PortersDB $porter_) {
		$this->porter = $porter_;
		if ($porter_ !== null)
			$this->setPorterId($porter_->getId());
	}

	public function getPorterId() {
		return $this->porterId;
	}

	public function setPorterId($porterId_) {
		$this->porterId = $porterId_;
	}

	public function getService() {
		return $this->service;
	}

	public function setService(ServicesDB $service_) {
		$this->service = $service_;
		if ($service_ !== null)
			$this->setServiceId($service_->getId());
	}

	public function getServiceId() {
		return $this->serviceId;
	}

	public function setServiceId($serviceId_) {
		$this->serviceId = $serviceId_;
	}

	public function getInterestGrade() {
		return $this->interestGrade;
	}

	public function getReadableInterestGrade() {
		switch($this->getInterestGrade()) {
			case 0:
				return '(0) not a chance, ever';
			case 1:
				return '(1) maybe once a year';
			case 2:
				return '(2) now and again but not often';
			case 3:
				return '(3) if my schedule is free I\'ll take it';
			case 4:
				return '(4) if you need me I\'ll be there';
			case 5:
				return '(5) the highest priority in my life';
		}

		return 'error with data';
	}

	public function setInterestGrade($interestGrade_) {
		$this->interestGrade = $interestGrade_;
	}

}