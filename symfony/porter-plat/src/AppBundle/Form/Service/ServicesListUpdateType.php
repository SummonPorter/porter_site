<?php

// src/AppBundle/Form/Service/ServicesListUpdateType.php
namespace AppBundle\Form\Service;

use AppBundle\Form\Service\ServiceType;
use AppBundle\Form\EntityListUpdateType;

class ServicesListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(ServiceType::class);
	}

}