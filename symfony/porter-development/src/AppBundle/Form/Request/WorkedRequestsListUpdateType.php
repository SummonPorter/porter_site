<?php

// src/AppBundle/Form/Request/WorkedRequestsListUpdateType.php
namespace AppBundle\Form\Request;

use AppBundle\Form\Request\WorkedUpdateRequestEntityType;
use AppBundle\Form\EntityListUpdateType;

class WorkedRequestsListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(WorkedUpdateRequestEntityType::class);
	}

}