<?php

// src/AppBundle/Services/TimeOperations.php
namespace AppBundle\Services;

use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CitiesDB;
use \DateTime;
use \DateTimeZone;

class TimeOperations {

	public function createStartDateTimeForRequest(RequestsDB $request_) {
		return $this->createDateTimeForRequest($request_->getRequestStartDateTime()->format('Y-m-d H:i:s'), $request_);
	}

	public function createDateTimeForRequest(string $time_ = null, RequestsDB $request_ = null) {
		if ($request_ !== null) {
			if ($request_->getRepCity() !== null) {
				$time = $this->createDateTimeForTimezone($time_, $request_->getRepCity()->getTimezone());
				if ($time !== null)
					return $time;
			}
		}

		return $this->createDateTimeForTimezone($time_, null);
	}

    //  returns null on failure or a new DateTime object
    public function createDateTimeForTimezone(string $time_ = null, string $timezone_ = null) {
        try {
        	$dateTimeZone = null;
        	if ($timezone_ !== null)
        		$dateTimeZone = new DateTimeZone($timezone_);

            $date = new DateTime($time_, $dateTimeZone);
            return $date;
        } catch (Exception $e) {
            return null;
        }
    }

}