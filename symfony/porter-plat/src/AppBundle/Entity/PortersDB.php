<?php

// src/AppBundle/Entity/PortersDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\CitiesDB;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;

/**
* @ORM\Entity
* @ORM\Table(name="porters")
*/
class PortersDB {

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
	* @ORM\Column(type="string", length=64, name="firstName")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $firstName;

	/**
	* @ORM\Column(type="string", length=64, name="lastName")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $lastName;

	/**
	* @ORM\Column(type="date", length=11, name="birthDate")
	*/
	private $birthDate;

	/**
	* @ORM\Column(type="string", length=32, name="mobilePhone")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $mobilePhone;

	/**
	* @ORM\Column(type="string", length=32, name="homePhone")
	*
	* @Assert\Type("string")
	*/
	private $homePhone;

	/**
	* @ORM\Column(type="string", length=128, name="email")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $email;

	/**
	* @ORM\Column(type="string", length=256, name="address")
	*
	* @Assert\Type("string")
	*/
	private $address;

	/**
	* @ORM\Column(type="string", length=10, name="mailing")
	*
	* @Assert\Type("string")
	*/
	private $mailing;

	/**
	* @ORM\ManyToOne(targetEntity="CitiesDB", fetch="EAGER")
	* @ORM\JoinColumn(name="cityId", referencedColumnName="id")
	*
	* @Assert\NotNull()
	*/
	private $city;

	/**
	* @ORM\Column(type="string", length=128, name="backgroundRef")
	*
	* @Assert\Type("string")
	*/
	private $backgroundRef;

	/**
	* @ORM\Column(type="integer", length=11, name="signupEpoch")
	*
	* @Assert\NotNull()
	* @Assert\Type("integer")
	*/
	private $signupEpoch;

	/**
	* @ORM\Column(type="boolean", name="active")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $active;

    /**
    * @ORM\OneToMany(targetEntity="PorterVehicleAccessDB", fetch="EAGER", mappedBy="porter", cascade={"persist", "merge", "remove"})
    *
    * @Assert\NotNull()
    */
    private $vehicles;

    /**
    * @ORM\OneToMany(targetEntity="ServiceInterestGradeDB", fetch="EAGER", mappedBy="porter", cascade={"persist", "merge", "remove"}, orphanRemoval=true)
    *
    * @Assert\NotNull()
    */
    private $serviceInterestGrades;

    public function __construct() {
        $this->vehicles = new ArrayCollection();
        $this->serviceInterestGrades = new ArrayCollection();
    }

    //========================================================================================
    //  Setters & Getters
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
    
    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName_) {
        $this->firstName = $firstName_;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName_) {
        $this->lastName = $lastName_;
    }

    public function getBirthDate() {
        return $this->birthDate;
    }

    public function setBirthDate($birthDate_) {
        $this->birthDate = $birthDate_;
    }

    public function getMobilePhone() {
        return $this->mobilePhone;
    }

    public function setMobilePhone($mobilePhone_) {
        $this->mobilePhone = $mobilePhone_;
    }

    public function getHomePhone() {
        return $this->homePhone;
    }

    public function setHomePhone($homePhone_) {
        $this->homePhone = $homePhone_;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email_) {
        $this->email = $email_;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address_) {
        $this->address = $address_;
    }

    public function getMailing() {
        return $this->mailing;
    }

    public function setMailing($mailing_) {
        $this->mailing = $mailing_;
    }

    public function getBackgroundRef() {
        return $this->backgroundRef;
    }

    public function setBackgroundRef($backgroundRef_) {
        $this->backgroundRef = $backgroundRef_;
    }

    public function getSignupEpoch() {
        return $this->signupEpoch;
    }

    public function setSignupEpoch($signupEpoch_) {
        $this->signupEpoch = $signupEpoch_;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active_) {
        $this->active = $active_;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity(CitiesDB $city_) {
        $this->city = $city_;
    }

    public function getVehicles() {
        if ($this->vehicles instanceof PersistentCollection)
            return $this->vehicles->getValues();
        if ($this->vehicles instanceof ArrayCollection)
            return $this->vehicles->getValues();
        return $this->vehicles;
    }

    public function setVehicles($vehicles_) {
        $this->vehicles = $vehicles_;

        $vehicleArray = $this->getVehicles();
        if ($vehicleArray !== null) {
            foreach ($vehicleArray as $vehicle)
                $vehicle->setPorter($this);
        }
    }
    
    public function getServiceInterestGrades() {
        if ($this->serviceInterestGrades instanceof PersistentCollection)
            return $this->serviceInterestGrades->getValues();
        if ($this->serviceInterestGrades instanceof ArrayCollection)
            return $this->serviceInterestGrades->getValues();
        return $this->serviceInterestGrades;
    }

    public function setServiceInterestGrades($serviceInterestGrades_) {
        $this->serviceInterestGrades = $serviceInterestGrades_;

        $serviceInterests = $this->getServiceInterestGrades();
        if ($serviceInterests !== null) {
            foreach ($serviceInterests as $interest)
                $interest->setPorter($this);
        }
    }

    //========================================================================================
    //  Misc
    //========================================================================================

    public function addVehicleAccess(PorterVehicleAccessDB $vehicleAccess_) {
        $this->vehicles->add($vehicleAccess_);
        $vehicleAccess_->setPorter($this);
    }

    public function getVehicleAccessCount() {
        return count($this->getVehicles());
    }

    public function addServiceInterestGrade(ServiceInterestGradeDB $serviceInterestGrade_) {
        $this->serviceInterestGrades->add($serviceInterestGrade_);
        $serviceInterestGrade_->setPorter($this);
    }

    public function removeServiceInterestGrade(ServiceInterestGradeDB $serviceInterestGrade_) {
        $this->serviceInterestGrades->removeElement($serviceInterestGrade_);
    }

    public function getInterestGradeForService(ServicesDB $service_) {
        $serviceInterests = $this->getServiceInterestGrades();
        if ($serviceInterests !== null) {
            foreach ($serviceInterests as $interest) {
                if ($interest->getService()->getId() === $service_->getId())
                    return $interest;
            }
        }

        return null;
    }

    public function getBirthDateString() {
        if ($this->getBirthDate() === null)
            return '';
        return $this->getBirthDate()->format('Y-m-d');
    }

    public function getSignupEpochString() {
        if ($this->getSignupEpoch() === null)
            return 0;
        if ($this->getSignupEpoch() === 0)
            return 0;
        $date = DateTime::createFromFormat('U', $this->getSignupEpoch());
        return $date->format('Y-m-d H:i:s');
    }

    public function getFullName() {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getFullNameAndCity() {
        $fullName = $this->getFullName();
        $city = '';

        if ($this->getCity() !== null) {
            $city = $this->getCity()->getNameAndDetails();
            return $fullName . '  |  ' . $city;
        }

        return $fullName;
    }
    
}