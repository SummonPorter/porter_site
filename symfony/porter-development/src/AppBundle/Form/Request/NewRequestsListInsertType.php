<?php

// src/AppBundle/Form/Request/NewRequestsListInsertType.php
namespace AppBundle\Form\Request;

use AppBundle\Form\Request\NewRequestEntityType;
use AppBundle\Form\EntityListInsertType;

class NewRequestsListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(NewRequestEntityType::class);
	}

}