<?php

// src/AppBundle/Form/ServiceGroup/ServiceGroupsListUpdateType.php
namespace AppBundle\Form\ServiceGroup;

use AppBundle\Form\ServiceGroup\ServiceGroupType;
use AppBundle\Form\EntityListUpdateType;

class ServiceGroupsListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(ServiceGroupType::class);
	}

}