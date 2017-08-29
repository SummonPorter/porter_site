<?php

// src/AppBundle/Form/ServiceGroup/ServiceGroupsListInsertType.php
namespace AppBundle\Form\ServiceGroup;

use AppBundle\Form\ServiceGroup\ServiceGroupType;
use AppBundle\Form\EntityListInsertType;

class ServiceGroupsListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(ServiceGroupType::class);
	}

}