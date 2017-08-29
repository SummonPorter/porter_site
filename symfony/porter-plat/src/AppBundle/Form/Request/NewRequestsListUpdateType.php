<?php

// src/AppBundle/Form/Request/NewRequestsListUpdateType.php
namespace AppBundle\Form\Request;

use AppBundle\Form\Request\NewUpdateRequestEntityType;
use AppBundle\Form\EntityListUpdateType;

class NewRequestsListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(NewUpdateRequestEntityType::class);
	}

}