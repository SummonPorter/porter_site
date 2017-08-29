<?php

// src/AppBundle/Form/Region/RegionsListUpdateType.php
namespace AppBundle\Form\Region;

use AppBundle\Form\Region\RegionType;
use AppBundle\Form\EntityListUpdateType;

class RegionsListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(RegionType::class);
	}

}