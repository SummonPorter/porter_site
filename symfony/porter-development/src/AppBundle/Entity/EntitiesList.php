<?php

// src/AppBundle/Entity/EntitiesList.php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class EntitiesList {

	/**
	* @Assert\NotNull()
	*/
	protected $entities;

	/**
	* @Assert\NotNull()
	* @Assert\Type("string")
	*/
	protected $listFunction;

	public function __construct($listFunction_ = null) {
		$this->entities = new ArrayCollection();
		$this->listFunction = $listFunction_;
	}

	public function getEntities() {
		return $this->entities;
	}

	public function setEntities($entities_) {
		$this->entities = $entities_;
	}

	public function addEntity($entity_) {
		$this->entities->add($entity_);
	}

	public function getListFunction() {
		return $this->listFunction;
	}

	public function setListFunction($listFunction_) {
		$this->listFunction = $listFunction_;
	}

}