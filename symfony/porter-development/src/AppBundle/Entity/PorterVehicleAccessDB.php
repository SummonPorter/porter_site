<?php

// src/AppBundle/Entity/PorterVehicleAccessDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServicesDB;

/**
* @ORM\Entity
* @ORM\Table(name="porterVehicleAccess")
*/
class PorterVehicleAccessDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
    * @ORM\ManyToOne(targetEntity="PortersDB", inversedBy="vehicles")
    * @ORM\JoinColumn(name="porterId", referencedColumnName="id")
    *
    * @Assert\NotNull()
	*/
	private $porter;

	/**
	* @ORM\Column(type="string", length=128, name="vehicleType")
	*
	* @Assert\Type("string")
	*/
	private $vehicleType;

	/**
	* @ORM\Column(type="boolean", name="personal")
	*
	* @Assert\Type("bool")
	*/
	private $personal;

	//	used for form processing
	private $porterId;

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getPorter() {
		return $this->porter;
	}

	public function setPorter(PortersDB $porter_) {
		$this->porter = $porter_;
		if ($porter_ !== null)
			$this->setPorterId($porter_->getId());
	}

	public function getVehicleType() {
		return $this->vehicleType;
	}

	public function setVehicleType($vehicleType_) {
		$this->vehicleType = $vehicleType_;
	}

	public function getPersonal() {
		return $this->personal;
	}

	public function setPersonal($personal_) {
		$this->personal = $personal_;
	}

	public function getPorterId() {
		return $this->porterId;
	}

	public function setPorterId($porterId_) {
		$this->porterId = $porterId_;
	}
	
}