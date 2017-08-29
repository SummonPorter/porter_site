<?php

// src/AppBundle/Form/City/CitiesListUpdateType.php
namespace AppBundle\Form\City;

use AppBundle\Form\City\CityType;
use AppBundle\Form\EntityListUpdateType;

class CitiesListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(CityType::class);
	}

}