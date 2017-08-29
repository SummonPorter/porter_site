<?php

// src/AppBundle/Entity/PorterAssignmentsDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\Table(name="porterAssignments")
*/
class PorterAssignmentsDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
	* @ORM\OneToOne(targetEntity="PortersDB")
	* @ORM\JoinColumn(name="porterId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $porter;

	/**
    * @ORM\ManyToOne(targetEntity="RequestsDB", inversedBy="porterAssignments")
    * @ORM\JoinColumn(name="requestId", referencedColumnName="id")
    *
    * @Assert\NotNull()
	*/
	private $request;

	/**
	* @ORM\Column(type="integer", length=11, name="assignmentEpoch")
	*
	* @Assert\Type("integer")
	*/
	private $assignmentEpoch;

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getPorter() {
		return $this->porter;
	}

	public function setPorter($porter_) {
		$this->porter = $porter_;
	}

	public function getRequest() {
		return $this->request;
	}

	public function setRequest($request_) {
		$this->request = $request_;
	}

	public function getAssignmentEpoch() {
		return $this->assignmentEpoch;
	}

	public function setAssignmentEpoch($assignmentEpoch_) {
		$this->assignmentEpoch = $assignmentEpoch_;
	}

}