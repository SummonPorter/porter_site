<?php

// src/AppBundle/Services/MathOperations.php
namespace AppBundle\Services;


class MathOperations {

	//========================================================================================
	//	Sorting & Filtering
	//========================================================================================

	//	iterates through array, removing any duplicate numbers found and returns new array
	public function removeDuplicates(Array $numbers_) {
		$size = count($numbers_);
		$newArray = [];
		$dictionary = [];

		for ($i = 0; $i < $size; $i++) {
			if (array_key_exists($numbers_[$i], $numbers_) === false) {
				$dictionary[$numbers_[$i]] = $numbers_[$i];
				$newArray[] = $numbers_[$i];
			}
		}

		return $newArray;
	}

	//========================================================================================
	//	Latitude & Longitude Methods
	//========================================================================================

	//	method assumes coordinates are provided as decimal degrees, uses haversine formula to determine distance and returns it
	public function distanceBetweenCoordinates($lat1_, $lon1_, $lat2_, $lon2_) {
		$lat1R 	= deg2rad($lat1_);			//	converted to radians
		$lon1R 	= deg2rad($lon1_);			//	converted to radians
		$lat2R 	= deg2rad($lat2_);			//	converted to radians
		$lon2R 	= deg2rad($lon2_);			//	converted to radians

		$R 		= 6371000;					//	radius of earth (approx)
		$dLatR 	= deg2rad($lat1_ - $lat2_);	//	difference between latitudes, in radians
		$dLonR	= deg2rad($lon1_ - $lon2_);	//	difference between longitudes, in radians

		$a		= (sin($dLatR / 2) * sin($dLatR / 2)) + (cos($lat1R) * cos($lat2R) * sin($dLonR / 2) * sin($dLonR / 2));
		$c		= 2 * atan2(sqrt($a), sqrt(1 - $a));

		return $R * $c;
	}

}