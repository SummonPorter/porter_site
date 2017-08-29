<?php

// src/AppBundle/Form/Region/RegionsListInsertType.php
namespace AppBundle\Form\Region;

use AppBundle\Form\Region\RegionType;
use AppBundle\Form\EntityListInsertType;

class RegionsListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(RegionType::class);
	}

}