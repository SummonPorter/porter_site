<?php

// src/AppBundle/Form/Country/CountriesListUpdateType.php
namespace AppBundle\Form\Country;

use AppBundle\Form\Country\CountryType;
use AppBundle\Form\EntityListUpdateType;

class CountriesListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(CountryType::class);
	}

}