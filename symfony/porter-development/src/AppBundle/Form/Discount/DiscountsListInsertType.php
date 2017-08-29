<?php

// src/AppBundle/Form/Discount/DiscountsListInsertType.php
namespace AppBundle\Form\Discount;

use AppBundle\Form\Discount\DiscountType;
use AppBundle\Form\EntityListInsertType;

class DiscountsListInsertType extends EntityListInsertType {

	public function __construct() {
		parent::__construct(DiscountType::class);
	}

}