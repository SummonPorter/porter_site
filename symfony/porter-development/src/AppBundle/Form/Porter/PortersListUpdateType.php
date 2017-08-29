<?php

// src/AppBundle/Form/Porter/PortersListUpdateType.php
namespace AppBundle\Form\Porter;

use AppBundle\Form\Porter\PorterType;
use AppBundle\Form\EntityListUpdateType;

class PortersListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(PorterType::class);
	}

}