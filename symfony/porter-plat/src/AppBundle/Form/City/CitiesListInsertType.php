<?php

// src/AppBundle/Form/City/CitiesListInsertType.php
namespace AppBundle\Form\City;

use AppBundle\Form\City\CityType;
use AppBundle\Form\EntityListInsertType;

class CitiesListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(CityType::class);
	}

}