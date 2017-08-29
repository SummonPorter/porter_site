<?php

// src/AppBundle/Security/SecurityOperations.php
namespace AppBundle\Security;

use AppBundle\Entity\CitiesDB;
use AppBundle\Entity\CountriesDB;
use AppBundle\Entity\RegionsDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Monolog\Logger;

class SecurityOperations {

    private $doctrine;
    private $logger;
    //  list of recently created porter tokens - tokens that are not yet present in the database
    private $porterTokens;
    //  list of recently created request tokens - tokens that are not yet present in the database
    private $requestTokens;

    public function __construct(Registry $doctrine_, Logger $logger_) {
        $this->doctrine = $doctrine_;
        $this->logger = $logger_;
        $this->porterTokens = [];
        $this->requestTokens = [];
    }

    //========================================================================================
    //  Public Facing Methods
    //========================================================================================

    //  returns a unique token for a porter
    public function getPorterToken(): string {
        $token = random_bytes(32);
        $token = bin2hex($token);

        while (!$this->porterTokenUnique($token)) {
            $token = random_bytes(32);
            $token = bin2hex($token);            
        }

        //  add token to porterTokens array
        $porterTokens[$token] = true;

        return $token;
    }

    //  returns a unique token for a request
    public function getRequestToken(): string {
        $token = random_bytes(64);
        $token = bin2hex($token);

        while (!$this->requestTokenUnique($token)) {
            $token = random_bytes(64);
            $token = bin2hex($token);            
        }

        //  add token to requestTokens array
        $requestTokens[$token] = true;

        return $token;
    }

    //  loops through array_ (and all sub arrays) and performs strip_tags on every element that is a string then returns the updated array
    public function stripTagsFromArray(array $data_) {
        foreach ($data_ as &$point) {
            if (is_string($point) === true)
                $point = strip_tags($point);
            elseif (is_array($point) === true)
                $point = $this->stripTagsFromArray($point);
        }

        return $data_;
    }

    //  TODO: implement this, probably make it more object oriented, needs to be a bit more flexible
    //  validationMap_ is an array containing all keys that need to exist in the request data
    //  returns true if all keys exist and have data or false if data was missing
    //  ex: ['first_name' => true, 'last_name' => true, 'optional' => false, 'hobbies' => ['location' => true, 'season' => false]]
    //  in above example, 'first_name' and 'last_name' must exist but 'optional' is optional

    //========================================================================================
    //  Protected Helper Methods
    //========================================================================================

    //  returns true if $token_ is unique and not found for any other porters
    protected function porterTokenUnique(string $token_): bool {
        //  check local tokens created
        if (array_key_exists($token_, $this->porterTokens))
            return false;

        //  query database
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('p.id'))
            ->from('AppBundle:PortersDB','p')
            ->where('p.idToken = \'' . $token_ . '\'');

        return intval($qb->getQuery()->getSingleScalarResult()) === 0;
    }

    //  returns true if $token_ is unique and not found for any other requests
    protected function requestTokenUnique(string $token_): bool {
        //  check local tokens created
        if (array_key_exists($token_, $this->requestTokens))
            return false;

        //  query database
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('r.id'))
            ->from('AppBundle:RequestsDB','r')
            ->where('r.idToken = \'' . $token_ . '\'');

        return intval($qb->getQuery()->getSingleScalarResult()) === 0;
    }

}