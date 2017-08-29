<?php

// src/AppBundle/Form/Service/ServicesListInsertType.php
namespace AppBundle\Form\Service;

use AppBundle\Form\Service\ServiceType;
use AppBundle\Form\EntityListInsertType;

class ServicesListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(ServiceType::class);
	}

}