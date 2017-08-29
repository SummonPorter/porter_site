<?php

// src/AppBundle/Entity/PorterRequestActivityDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\RequestsDB;
use \DateTime;
/**
* @ORM\Entity
* @ORM\Table(name="porterRequestActivity")
*/
class PorterRequestActivityDB {

	/**
	* @ORM\Id
    * @ORM\Column(type="integer", length=10, name="id")
    * @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
    * @ORM\ManyToOne(targetEntity="PortersDB")
    * @ORM\JoinColumn(name="porterId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $porter;

	/**
    * @ORM\ManyToOne(targetEntity="RequestsDB")
    * @ORM\JoinColumn(name="requestId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $request;

	/**
	* @ORM\Column(type="datetime", length=11, name="activityDateTime")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\DateTime()
	*/
	private $activityDateTime;

	/**
	* @ORM\Column(type="string", length=32, name="activity")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $activity;

	//	used for forms
	private $porterId;
	private $requestId;

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

	public function setPorter(PortersDB $porter_ = null) {
		$this->porter = $porter_;
		if ($porter_ !== null)
			$this->setPorterId($porter_->getId());
	}

	public function getRequest() {
		return $this->request;
	}

	public function setRequest(RequestsDB $request_ = null) {
		$this->request = $request_;
		if ($request_ !== null)
			$this->setRequestId($request_->getId());
	}

	public function getActivityDateTime() {
		return $this->activityDateTime;
	}

	public function setActivityDateTime($activityDateTime_) {
		$this->activityDateTime = $activityDateTime_;
	}

	public function getActivity() {
		return $this->activity;
	}

	public function setActivity($activity_) {
		$this->activity = $activity_;
	}

	public function getPorterId() {
		return $this->porterId;
	}

	public function setPorterId($porterId_) {
		$this->porterId = $porterId_;
	}

	public function getRequestId() {
		return $this->requestId;
	}

	public function setRequestId($requestId_) {
		$this->requestId = $requestId_;
	}

	//========================================================================================
	//	Misc
	//========================================================================================

	/*public function getActivityEpochString() {
		$date = DateTime::createFromFormat('U', round($this->getActivityEpoch() / 1000));
		return $date->format('Y-m-d H:i:s');
	}*/

}