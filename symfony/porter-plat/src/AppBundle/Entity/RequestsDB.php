<?php

// src/AppBundle/Entity/RequestsDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\DiscountsDB;
use AppBundle\Entity\PorterAssignmentsDB;
use AppBundle\Entity\RequestAddressesDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\PersistentCollection;
use \DateTime;

/**
* @ORM\Entity
* @ORM\Table(name="requests")
*/
class RequestsDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
    * @ORM\Column(type="string", length=128, name="idToken")
    *
    * @Assert\Type("string")
    */
    private $idToken;

	/**
    * @ORM\ManyToOne(targetEntity="ServicesDB", fetch="EAGER")
    * @ORM\JoinColumn(name="serviceId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $service;

	/**
    * @ORM\ManyToOne(targetEntity="DiscountsDB", fetch="EAGER")
    * @ORM\JoinColumn(name="discountId", referencedColumnName="id")
	*/
	private $discount;

	/**
	* @ORM\Column(type="smallint", length=5, name="portersWanted")
	*
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $portersWanted;

	/**
    * @ORM\OneToMany(targetEntity="PorterAssignmentsDB", mappedBy="request", cascade={"merge", "persist", "remove"}, fetch="EAGER")
	*/
	private $porterAssignments;

	/**
	* @ORM\Column(type="string", length=256, name="requestState")
	*
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $requestState;

	/**
	* @ORM\ManyToOne(targetEntity="CustomersDB", cascade={"persist", "merge"})
	* @ORM\JoinColumn(name="customerId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $customer;

	/**
	* @ORM\Column(type="datetime", length=11, name="porterStartDateTime")
	*
	* @Assert\DateTime()
	*/
	private $porterStartDateTime;

	/**
	* @ORM\Column(type="datetime", length=11, name="porterEndDateTime")
	*
	* @Assert\DateTime()
	*/
	private $porterEndDateTime;

	/**
	* @ORM\Column(type="float", name="billingSubtotal")
	*/
	private $billingSubtotal;

	/**
	* @ORM\Column(type="float", name="billingService")
	*/
	private $billingService;

	/**
	* @ORM\Column(type="float", name="billingDiscount")
	*/
	private $billingDiscount;

	/**
	* @ORM\Column(type="float", name="billingTaxes")
	*/
	private $billingTaxes;

	/**
	* @ORM\Column(type="float", name="billingTrustFee")
	*/
	private $billingTrustFee;

	/**
	* @ORM\Column(type="float", name="billingTotal")
	*/
	private $billingTotal;

	/**
	* @ORM\Column(type="integer", length=11, name="requestEpoch")
	*
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $requestEpoch;

	/**
	* @ORM\Column(type="datetime", length=11, name="requestStartDateTime")
	*
	* @Assert\NotNull()
	* @Assert\DateTime()
	*/
	private $requestStartDateTime;

	/**
	* @ORM\Column(type="smallint", length=5, name="estDuration")
	*
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $estDuration;

	/**
	* @ORM\Column(type="string", length=2048, name="details")
	*
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $details;

	/**
    * @ORM\OneToMany(targetEntity="RequestAddressesDB", fetch="EAGER", mappedBy="request", cascade={"persist", "merge", "remove"}, orphanRemoval=true)
    *
    * @Assert\NotNull()
    */
    private $addresses;

	public function __construct() {
		$this->porterAssignments = new ArrayCollection();
		$this->serviceInterestGrades = new ArrayCollection();
		$this->addresses = new ArrayCollection();
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getIdToken() {
        return $this->idToken;
    }

    public function setIdToken($idToken_) {
        $this->idToken = $idToken_;
    }

	public function getService() {
		return $this->service;
	}

	public function setService(ServicesDB $service_) {
		$this->service = $service_;
	}

	public function getDiscount() {
		return $this->discount;
	}

	public function setDiscount(DiscountsDB $discount_ = null) {
		$this->discount = $discount_;
	}

	public function getPortersWanted() {
		return $this->portersWanted;
	}

	public function setPortersWanted($portersWanted_) {
		$this->portersWanted = $portersWanted_;
	}

	public function getPorterAssignments() {
		if ($this->porterAssignments instanceof PersistentCollection)
			return $this->porterAssignments->getValues();
		if ($this->porterAssignments instanceof ArrayCollection)
			return $this->porterAssignments->getValues();
		return $this->porterAssignments;
	}

	public function setPorterAssignments($porterAssignments_) {
		$this->porterAssignments = $porterAssignments_;
	}

	public function getRequestState() {
		return $this->requestState;
	}

	public function setRequestState($requestState_) {
		$this->requestState = $requestState_;
	}

	public function getCustomer() {
		return $this->customer;
	}

	public function setCustomer($customer_) {
		$this->customer = $customer_;
	}

	public function getPorterStartDateTime() {
		return $this->porterStartDateTime;
	}

	public function setPorterStartDateTime($porterStartDateTime_) {
		$this->porterStartDateTime = $porterStartDateTime_;
	}

	public function getPorterEndDateTime() {
		return $this->porterEndDateTime;
	}

	public function setPorterEndDateTime($porterEndDateTime_) {
		$this->porterEndDateTime = $porterEndDateTime_;
	}

	public function getBillingSubtotal() {
		return $this->billingSubtotal;
	}

	public function setBillingSubtotal($billingSubtotal_) {
		$this->billingSubtotal = $billingSubtotal_;
	}

	public function getBillingService() {
		return $this->billingService;
	}

	public function setBillingService($billingService_) {
		$this->billingService = $billingService_;
	}

	public function getBillingDiscount() {
		return $this->billingDiscount;
	}

	public function setBillingDiscount($billingDiscount_) {
		$this->billingDiscount = $billingDiscount_;
	}

	public function getBillingTaxes() {
		return $this->billingTaxes;
	}

	public function setBillingTaxes($billingTaxes_) {
		$this->billingTaxes = $billingTaxes_;
	}

	public function getBillingTrustFee() {
		return $this->billingTrustFee;
	}

	public function setBillingTrustFee($billingTrustFee_) {
		$this->billingTrustFee = $billingTrustFee_;
	}

	public function getBillingTotal() {
		return $this->billingTotal;
	}

	public function setBillingTotal($billingTotal_) {
		$this->billingTotal = $billingTotal_;
	}

	public function getRequestEpoch() {
		return $this->requestEpoch;
	}

	public function setRequestEpoch($requestEpoch_) {
		$this->requestEpoch = $requestEpoch_;
	}

	public function getRequestStartDateTime() {
		return $this->requestStartDateTime;
	}

	public function setRequestStartDateTime($requestStartDateTime_) {
		$this->requestStartDateTime = $requestStartDateTime_;
	}

	public function getEstDuration() {
		return $this->estDuration;
	}

	public function setEstDuration($estDuration_) {
		$this->estDuration = $estDuration_;
	}

	public function getDetails() {
		return $this->details;
	}

	public function setDetails($details_) {
		$this->details = $details_;
	}

	public function getAddresses() {
		if ($this->addresses instanceof PersistentCollection)
			return $this->addresses->getValues();
		if ($this->addresses instanceof ArrayCollection)
			return $this->addresses->getValues();
		return $this->addresses;
	}

	public function setAddresses($addresses_) {
		$this->addresses = $addresses_;
	}

	//========================================================================================
	//	Misc
	//========================================================================================

	public function getAssignedPorters() {
		$assignments = $this->getPorterAssignments();
		$porters = [];

		foreach ($assignments as $assignment)
			$porters[] = $assignment->getPorter();

		return $porters;
	}

	public function addPorterAssignment(PorterAssignmentsDB $porterAssignment_) {
		if ($porterAssignment_ === null)
			return;

        $this->porterAssignments->add($porterAssignment_);
        $porterAssignment_->setRequest($this);
    }

    public function getAssignedPorterCount() {
        return count($this->getPorterAssignments());
    }

    public function hasPorterAssigned(PortersDB $porter_) {
    	if ($porter_ === null)
    		return false;

    	$assignedPorters = $this->getAssignedPorters();
    	foreach($assignedPorters as $assignedPorter) {
    		if ($assignedPorter->getId() === $porter_->getId())
    			return true;
    	}

    	return false;
    }

    public function addAddress(RequestAddressesDB $address_) {
		if ($address_ === null)
			return;

        $this->addresses->add($address_);
        $address_->setRequest($this);
    }

    //	returns the city from the address that has the lowest stepping for this request
    public function getRepCity() {
    	$addresses = $this->getAddresses();

    	if (empty($addresses))
    		return null;
    	if (count($addresses) === 1)
    		return $addresses[0]->getCity();

    	$bestStepping = null;
    	foreach ($addresses as $address) {
    		if ($bestStepping === null)
    			$bestStepping = $address;
    		elseif ($address->getStepping() < $bestStepping->getStepping())
    			$bestStepping = $address;
    	}
    	return $bestStepping->getCity();
    }

	public function getPorterDurationInMin() {
		if ($this->getPorterStartDateTime() === null || $this->getPorterEndDateTime() === null)
			return 0;
		$startEpoch = $this->getPorterStartDateTime()->format('U');
		$endEpoch = $this->getPorterEndDateTime()->format('U');
		$result = (int)round(($endEpoch - $startEpoch) / 60);

		//	safety
		if ($result < 0)
			$result = 0;

		return $result;
	}

	public function getRequestEpochString() {
		$date = DateTime::createFromFormat('U', $this->getRequestEpoch());
		return $date->format('Y-m-d H:i:s');
	}

}