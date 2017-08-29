<?php

// src/AppBundle/Entity/ServiceGroupsDB.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;

/**
* @ORM\Entity
* @ORM\Table(name="serviceGroups")
*/
class ServiceGroupsDB {

	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10, name="id")
	* @ORM\GeneratedValue(strategy="AUTO")
	*
	* @Assert\Type("integer")
	*/
	private $id;

	/**
	* @ORM\Column(type="string", length=64, name="name")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $name;

	/**
	* @ORM\Column(type="string", length=256, name="description")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $description;

	/**
	* @ORM\Column(type="string", length=256, name="webImage")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $webImage;

	/**
	* @ORM\Column(type="string", length=256, name="appImage")
	*
	* @Assert\NotBlank()
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	private $appImage;

	/**
	* @ORM\Column(type="boolean", name="enabled")
	*
	* @Assert\NotNull()
	* @Assert\Type("bool")
	*/
	private $enabled;

	/**
    * @ORM\OneToMany(targetEntity="ServicesDB", mappedBy="group")
	*/
	private $services;

	public function __construct() {
		$this->services = new ArrayCollection();
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name_) {
		$this->name = $name_;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description_) {
		$this->description = $description_;
	}

	public function getWebImage() {
		return $this->webImage;
	}

	public function setWebImage($webImage_) {
		$this->webImage = $webImage_;
	}

	public function getAppImage() {
		return $this->appImage;
	}

	public function setAppImage($appImage_) {
		$this->appImage = $appImage_;
	}

	public function getEnabled() {
		return $this->enabled;
	}

	public function setEnabled($enabled_) {
		$this->enabled = $enabled_;
	}

	public function getServices() {
		if ($this->services instanceof PersistentCollection)
			return $this->services->getValues();
		if ($this->services instanceof ArrayCollection)
			return $this->services->getValues();
		return $this->services;
	}

	public function setServices($services_) {
		$this->services = $services_;
	}

}