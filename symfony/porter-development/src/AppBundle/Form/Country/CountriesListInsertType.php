<?php

// src/AppBundle/Form/Country/CountriesListInsertType.php
namespace AppBundle\Form\Country;

use AppBundle\Form\Country\CountryType;
use AppBundle\Form\EntityListInsertType;

class CountriesListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(CountryType::class);
	}

}