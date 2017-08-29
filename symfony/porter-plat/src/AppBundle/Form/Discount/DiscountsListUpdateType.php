<?php

// src/AppBundle/Form/Discount/DiscountsListUpdateType.php
namespace AppBundle\Form\Discount;

use AppBundle\Form\Discount\DiscountType;
use AppBundle\Form\EntityListUpdateType;

class DiscountsListUpdateType extends EntityListUpdateType {

	public function __construct() {
		parent::__construct(DiscountType::class);
	}

}