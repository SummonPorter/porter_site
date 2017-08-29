<?php

// src/AppBundle/Utilities/Misc/LocationValidator.php
namespace AppBundle\Utilities\Misc;

use AppBundle\Utilities\Misc\RequestValidator;
use AppBundle\Security\SecurityOperations;
use AppBundle\Entity\CitiesDB;
use AppBundle\Entity\RegionsDB;
use AppBundle\Entity\CountriesDB;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class LocationValidator extends RequestValidator {

	//	doctrine service
	private $doctrine;
	//	field where location data can be found
	private $locationKey;
	//	matches from regex
	private $matches;
	//	city found from search
	private $city;
	//	region found from search
	private $region;
	//	country found from search
	private $country;

	//========================================================================================
	//	Constructor
	//========================================================================================

	public function __construct(Logger $logger_, SecurityOperations $security_, Request $request_, string $dataMethod_) {
		parent::__construct($logger_, $security_, $request_, $dataMethod_);

		$this->doctrine 	= null;

		$this->setLocationKey(null);
		
		$this->matches 		= [];

		$this->city 		= null;
		$this->region 		= null;
		$this->country 		= null;
	}

	//========================================================================================
	//	Setters & Getters
	//========================================================================================

	public function setDoctrine(Registry $doctrine_) {
		$this->doctrine = $doctrine_;
	}

	public function setLocationKey(string $key_ = null) {
		$this->locationKey = $key_;
		if ($this->locationKey === null)
			$this->locationKey = 'null';
	}

	public function getLocationKey() {
		return $this->locationKey;
	}

	protected function setCity($city_) {
		$this->city = $city_;
	}

	public function getCity() {
		return $this->city;
	}

	protected function setRegion($region_) {
		$this->region = $region_;
	}

	public function getRegion() {
		return $this->region;
	}

	protected function setCountry($country_) {
		$this->country = $country_;
	}

	public function getCountry() {
		return $this->country;
	}

	//========================================================================================
	//	Runs the validator
	//========================================================================================

	//	uses the input to build a profile which outlines if a city, region, and country were found or not
	public function runValidation(): bool {
		//	if basic validation is false, return
		if (parent::runValidation() === false)
			return false;

		//	if validating the elements discovers an error, return
		if ($this->setDataValidAndMsg($this->validateData(), 'Sorry, information appears to be missing. Please check your information and try again.', null) === false)
			return false;

		//	getting list of words matched from input
		$this->matches = $this->pullMatches();

		//	if there were too few matches, return
		if ($this->setDataValidAndMsg((count($this->matches) >= 2), 'Sorry, the address you provided looks incomplete. Please provide a valid address.', null) === false)
			return false;
		
		if ($this->setDataValidAndMsg($this->findLocations(), null, null) === false) {
			if ($this->region === null)
				$this->addReturnMessage('Sorry, the address you provided is not supported. Porter is currently only available in British Columbia, Canada.');
			elseif ($this->city === null)
				$this->addReturnMessage('Sorry, the city you provided is not supported but Porter is working hard on expanding!');
			return false;
		}

		return $this->isDataValid();
	}

	//========================================================================================
	//	Validation
	//========================================================================================

	//	ensures data is valid (exists) and returns true or false depending
	protected function validateData(): bool {
		return $this->validateArrayElement($this->getRequestData(), $this->getLocationKey());
	}

	private function pullMatches(): array {
		$allMatches = [];
		preg_match_all($this->buildPattern(), $this->getRequestData()[$this->getLocationKey()], $allMatches);
		
		//	grabbing the matches specific to the first group
		$allMatches = $allMatches[1];

		//	reducing all matches for lower case for search reasons
		foreach ($allMatches as &$match)
			$match = strtolower($match);

		//	return matches
		return $allMatches;
	}

	private function buildPattern(): string {
		$subPatternClass 	= '[-!\[\]\(\)\<\>@#$%^&*`~+=\-0-9]';
		$negLookBehind		= '(?<!' . $subPatternClass . ')';
		$negLookAhead		= '(?!' . $subPatternClass . ')';
		return	 			  '/(' . $negLookBehind . '\b[a-zA-Z]{2,}\b' . $negLookAhead . ')/';
	}

	//	searching operates from right to left, from less specific to more specific
	//	TODO: improve intelligence - the algorithm is assuming the input will be formatted as city, region, country
	//	TODO: improve intelligence v2 - algorithm also assumes that each component (city, region, country) is just one word, kinda
	//	HACK: if the first search (country) doesn't find any matches, assume country was omitted and that the country is Canada and that the first word is the region instead
	//	returns false if not all components of the location could be found - or returns true of a city, region and country were all found
	private function findLocations(): bool {
		$matchCount = count($this->matches);
		$matchIndex = $matchCount - 1;

		//	HACK: temporary variables used to search for the longer versions of location elements
		$longCountry 	= null;
		$longRegion 	= null;
		$longCity		= null;

		$this->country = $this->findCountry($this->matches[$matchIndex]);
		//	reducing index
		if ($this->country !== null)
			$matchIndex--;

		//	HACK: a bit messy, let's try and match up to 2 words for the country
		if ($this->country === null && $matchIndex > 1) {
			$countryName = $this->matches[($matchIndex - 1)] . ' ' . $this->matches[$matchIndex];
			$this->longCountry = $this->findCountry($countryName);
			if ($longCountry !== null) {
				$this->country = $longCountry;
				$matchIndex -= 2;
			}
		}

		//	if null, assume country is canada and 1st string is region
		if ($this->country === null)
			$this->country = $this->findCanada();

		//	safety
		if ($matchIndex < 0)
			return false;

		$this->region = $this->findRegion($this->matches[$matchIndex], $this->country);
		//	HACK: a bit messy, let's try and match up to 2 words for the region
		if ($matchIndex > 1) {
			$regionName = $this->matches[($matchIndex - 1)] . ' ' . $this->matches[$matchIndex];
			$longRegion = $this->findRegion($regionName, $this->country);
			if ($longRegion !== null) {
				$this->region = $longRegion;
				$matchIndex -= 2;
			}
			else
				$matchIndex--;
		}
		else
			$matchIndex--;

		//	safety
		if ($matchIndex < 0 || $this->region === null)
			return false;

		$this->city = $this->findCity($this->matches[$matchIndex], $this->region);
		//	HACK: a bit messy, let's try and match up to 2 words for the city
		if ($matchIndex > 0) {
			$cityName = $this->matches[($matchIndex - 1)] . ' ' . $this->matches[$matchIndex];
			$longCity = $this->findCity($cityName, $this->region);
			if ($longCity !== null)
				$this->city = $longCity;
		}

		return $this->city !== null;
	}

	//========================================================================================
	//	Doctrine
	//========================================================================================

	//	returns CountriesDB or null
	private function findCountry(string $string_) {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CountriesDB', 'c')
            ->andWhere('c.enabled = true');

        //	if the string is only 2 letters, use the short name
		if (strlen($string_) === 2)
	        $qb->andWhere('LOWER(c.shortName) = ?1');
	    else 
            $qb->andWhere('LOWER(c.name) = ?1');

        //	setting parameter
        $qb->setParameter(1, $string_);

        return $qb->getQuery()->getOneOrNullResult();
	}

	//	used as hack
	private function findCanada() {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CountriesDB', 'c')
            ->where('LOWER(c.name) = \'canada\'');

        return $qb->getQuery()->getOneOrNullResult();
	}

	//	returns RegionsDB or null
	private function findRegion(string $string_, CountriesDB $country_) {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RegionsDB', 'r')
            ->where('r.country = ' . $country_->getId())
            ->andWhere('r.enabled = true');

        //	if the string is only 2 letters, use the short name
		if (strlen($string_) === 2)
	        $qb->andWhere('LOWER(r.shortName) = ?1');
	    else
            $qb->andWhere('LOWER(r.name) = ?1');

        //	setting parameter
        $qb->setParameter(1, $string_);

        return $qb->getQuery()->getOneOrNullResult();
	}

	//	returns CitiesDB or null
	private function findCity(string $string_, RegionsDB $region_) {
		$this->getLogger()->critical('finding city with string: ' . $string_);
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CitiesDB', 'c')
            ->where('c.region = ' . $region_->getId())
            ->andWhere('LOWER(c.name) = ?1')
            ->andWhere('c.enabled = true')
            ->setParameter(1, $string_);

        return $qb->getQuery()->getOneOrNullResult();
	}

	//========================================================================================
	//	JsonSerializable
	//========================================================================================

	public function jsonSerialize() {
		$parentData = parent::jsonSerialize();

		$cityName = "";
		if ($this->city !== null)
			$cityName = $this->city->getName();
		$regionName = "";
		if ($this->region !== null)
			$regionName = $this->region->getName();
		$countryName = "";
		if ($this->country !== null)
			$countryName = $this->country->getName();

		$parentData['city']		= $cityName;
		$parentData['region']	= $regionName;
		$parentData['country']	= $countryName;

		return $parentData;
    }

}