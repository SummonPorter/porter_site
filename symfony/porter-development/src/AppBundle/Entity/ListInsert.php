<?php

// src/AppBundle/Entity/ListInsert.php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\CountriesDB;
use Symfony\Component\Validator\Constraints as Assert;

class ListInsert {

	/**
	* @Assert\NotBlank()
	* @Assert\Type("integer")
	*/
	protected $insert;

	public function getInsert() {
		return $this->insert;
	}

	public function setInsert($insert_) {
		$this->insert = $insert_;
	}

}