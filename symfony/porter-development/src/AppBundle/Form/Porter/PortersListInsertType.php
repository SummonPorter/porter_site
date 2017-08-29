<?php

// src/AppBundle/Form/Porter/PortersListInsertType.php
namespace AppBundle\Form\Porter;

use AppBundle\Form\Porter\PorterType;
use AppBundle\Form\EntityListInsertType;

class PortersListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(PorterType::class);
	}

}