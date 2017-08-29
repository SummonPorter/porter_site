<?php

// src/AppBundle/Services/Database.php
namespace AppBundle\Services;

use AppBundle\Entity\CitiesDB;
use AppBundle\Entity\CountriesDB;
use AppBundle\Entity\RegionsDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

class Database {

    private $doctrine;

    public function __construct(Registry $doctrine_) {
        $this->doctrine = $doctrine_;
    }

    public function getInternalUsers() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('AppBundle:InternalUsersDB', 'u')
            ->where('u.enabled = true');

        return $qb->getQuery()->getResult();
    }

    public function getCities(bool $enabledOnly_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CitiesDB', 'c')
            ->innerJoin('AppBundle:CountriesDB', 'co', 'WHERE', 'c.country = co')
            ->innerJoin('AppBundle:RegionsDB', 'r', 'WHERE', 'c.region = r');

        if ($enabledOnly_) {
            $qb->where('c.enabled = true');
            $qb->andWhere('co.enabled = true');
        }

        $qb->orderBy('c.name', 'ASC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getCitiesForRegion(int $regionId_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CitiesDB', 'c')
            ->where('c.region = ' . $regionId_);

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    //  HACK: hardcoded to Vancouver for now
    //  returns a default city
    public function getDefaultCity() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CitiesDB', 'c')
            ->where('LOWER(c.name) = ?1')
            ->setParameter(1, 'vancouver');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCity(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CitiesDB', 'c')
            ->where('c.id = ' . $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getCitiesCount() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('c.id'));
        $qb->from('AppBundle:CitiesDB','c');

        return intval($qb->getQuery()->getSingleScalarResult());            //  !!!: returns string, not number?
    }

    public function getCitiesReferenceCount(int $cityId_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('p.id'))
            ->from('AppBundle:PortersDB', 'p')
            ->where('p.city = ' . $cityId_);

        $results = $qb->getQuery()->getSingleScalarResult();

        if ($clear_)
            $em->clear();

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    public function getCountries(bool $enabledOnly_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CountriesDB', 'c');

        if ($enabledOnly_)
            $qb->where('c.enabled = true');

        $qb->orderBy('c.name', 'ASC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getCountry(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CountriesDB', 'c')
            ->where('c.id = ' . $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getCountriesCount() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('c.id'));
        $qb->from('AppBundle:CountriesDB','c');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getRegions(bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RegionsDB', 'r');

        $qb->orderBy('r.name', 'ASC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getRegionsForCountry(int $countryId_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RegionsDB', 'r')
            ->where('r.country = ' . $countryId_);

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getRegion(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RegionsDB', 'r')
            ->where('r.id = ' . $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getRegionsCount() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('r.id'));
        $qb->from('AppBundle:RegionsDB','r');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getServices(bool $enabledOnly_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('AppBundle:ServicesDB', 's');

        if ($enabledOnly_)
            $qb->where('s.enabled = true');

        $qb->orderBy('s.name', 'ASC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getServicesForServiceGroup(int $serviceGroupId_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('AppBundle:ServicesDB', 's')
            ->where('s.group = ' . $serviceGroupId_);

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getService(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('AppBundle:ServicesDB', 's')
            ->where('s.id = ?1')
            ->setParameter(1, $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getServicesCount() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('s.id'));
        $qb->from('AppBundle:ServicesDB','s');

        return $qb->getQuery()->getSingleScalarResult();
    }

    //  returns number of requests that reference the service
    public function getServicesReferenceCount(int $serviceId_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('r.id'))
            ->from('AppBundle:RequestsDB', 'r')
            ->where('r.service = ' . $serviceId_);

        $results = $qb->getQuery()->getSingleScalarResult();

        if ($clear_)
            $em->clear();

        return intval($qb->getQuery()->getSingleScalarResult());
    }

    public function getServiceGroups(bool $enabledOnly_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('AppBundle:ServiceGroupsDB', 's');

        if ($enabledOnly_)
            $qb->where('s.enabled = true');

        $qb->orderBy('s.name', 'ASC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getServiceGroup(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('AppBundle:ServiceGroupsDB', 's')
            ->where('s.id = ' . $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getServiceGroupsCount() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('s.id'));
        $qb->from('AppBundle:ServiceGroupsDB','s');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getDiscounts(bool $validOnly_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('d')
            ->from('AppBundle:DiscountsDB', 'd');

        //  only discounts that are coming up or in-effect 
        //if ($validOnly_)
        //    $qb->where('d.parentCount = 0');

        $qb->orderBy('d.startEpoch', 'DESC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getDiscount(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('d')
            ->from('AppBundle:DiscountsDB', 'd')
            ->where('d.id = ' . $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getDiscountsCount() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('d.id'));
        $qb->from('AppBundle:DiscountsDB','d');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getPorters(bool $activeOnly_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:PortersDB', 'p');

        if ($activeOnly_)
            $qb->where('p.active = true');

        $qb->orderBy('p.lastName', 'ASC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getPorter(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:PortersDB', 'p')
            ->where('p.id = ' . $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getPorterFromToken(string $token_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:PortersDB', 'p')
            ->where('p.idToken = \'' . $token_ . '\'');

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getServiceInterestGrade() {
        $qb = $em->createQueryBuilder();
        $qb->select('ig')
            ->from('AppBundle:ServiceInterestGradeDB', 'ig');
        return $qb->getQuery()->getResult();
    }

    public function getPortersCount() {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('p.id'));
        $qb->from('AppBundle:PortersDB','p');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getRequests($filter_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RequestsDB', 'r')
            ->where('r.requestState = \'' . $filter_ . '\'')
            ->orderBy('r.requestStartDateTime', 'DESC');

        $results = $qb->getQuery()->getResult();

        if ($clear_)
            $em->clear();

        return $results;
    }

    public function getRequest(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RequestsDB', 'r')
            ->where('r.id = ' . $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getRequestAddress(int $id_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RequestAddressesDB', 'r')
            ->where('r.id = ?1')
            ->setParameter(1, $id_);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    public function getRequestFromToken(string $token_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from('AppBundle:RequestsDB', 'r')
            ->where('r.idToken = \'' . $token_ . '\'');

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($clear_)
            $em->clear();

        return $result;
    }

    //  returns an array of PorterRequestActivityDB objects for the porter
    public function getPorterActivities(RequestsDB $request_, PortersDB $porter_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from('AppBundle:PorterRequestActivityDB', 'a')
            ->where('a.porter = ' . $porter_->getId())
            ->andWhere('a.request = ' . $request_->getId());

        if ($clear_)
            $em->clear();

        return $results = $qb->getQuery()->getResult();
    }

    public function addEntities(array $entities_, bool $clear_ = false, bool $flush_ = false) {
        $em = $this->doctrine->getManager();

        foreach ($entities_ as &$e)
            $e = $em->persist($e);

        if ($flush_)
            $em->flush();
        if ($clear_)
            $em->clear();
    }

    public function updateEntities(array $entities_, bool $clear_ = false, bool $flush_ = false) {
        $em = $this->doctrine->getManager();

        foreach ($entities_ as &$e)
            $e = $em->merge($e);

        if ($flush_)
            $em->flush();
        if ($clear_)
            $em->clear();
    }

    public function deleteEntities(array $entities_, bool $clear_ = false, bool $flush_ = false) {
        $em = $this->doctrine->getManager();

        foreach ($entities_ as $e)
            $em->remove($em->merge($e));

        if ($flush_)
            $em->flush();
        if ($clear_)
            $em->clear();
    }

    public function deleteIdsIn(array $ids_, string $entityType_, bool $clear_ = false, bool $flush_ = false) {
        if (count($ids_) > 0) {
            $em = $this->doctrine->getManager();
            $qb = $em->createQueryBuilder();
            $qb->select('e')
                ->from($entityType_, 'e')
                ->where($qb->expr()->in('e.id', implode(',', $ids_)));

            $result = $qb->getQuery()->getResult();
            foreach ($result as $e)
                $em->remove($em->merge($e));

            if ($flush_)
                $em->flush();
            if ($clear_)
                $em->clear();
        }
    }

    public function getEntitiesFor(string $entityType_, bool $clear_ = false) {
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from($entityType_, 'e');
            
        $results = $qb->getQuery()->getResult();
        
        if ($clear_)
            $em->clear();

        return $results;
    }

}