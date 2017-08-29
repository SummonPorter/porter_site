<?php

// src/AppBundle/Form/Request/ActiveRequestsListUpdateType.php
namespace AppBundle\Form\Request;

use AppBundle\Form\Request\NewUpdateRequestEntityType;
use AppBundle\Form\EntityListUpdateType;

class ActiveRequestsListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(ActiveUpdateRequestEntityType::class);
	}

}