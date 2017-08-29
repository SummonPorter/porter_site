<?php

// src/AppBundle/Utilities/FormHandler/Specialized/PortersUpdateFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Utilities\FormHandler\UpdateFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class PortersUpdateFormHandler extends UpdateFormHandler {

	//========================================================================================
    //  Overridden
    //========================================================================================

    //  called before updating an entity to perform any special operations that may be necessary for extending classes
    protected function preUpdateOps(CmsOperations $cmsOperations_, Request $request_, $entity_) {
        //  get porter from database to so the idToken can be retained
        $original = $cmsOperations_->getDatabase()->getPorter($entity_->getId(), true);
        //  setting idToken
        $entity_->setIdToken($original->getIdToken());

        //  loop through vehicle access, generate warnings and ignore values if settings are not both configured
        $vehicles = $entity_->getVehicles();
        $nVehicles = new ArrayCollection();
        foreach ($vehicles as $access) {
            if ($access->getVehicleType() !== null && $access->getPersonal() !== null)
                $nVehicles->add($access);
            else {
                //  cleanup - if the access has an id, it exists in the database and must be removed
                if ($access->getId() !== null) {
                    $cmsOperations_->getDatabase()->deleteEntities([$access]);
                    $this->addWarningMessage($cmsOperations_, $request_->getSession(), 'an existing vehicle access property was not configured properly, and was therefore removed.');
                }
                elseif ($access->getVehicleType() !== null || $access->getPersonal() !== null)
                    $this->addWarningMessage($cmsOperations_, $request_->getSession(), 'a vehicle access property was not configured properly, and was therefore not saved.');
            }
        }
        $entity_->setVehicles($nVehicles);

        //  get services
        $dbServices = $cmsOperations_->getDatabase()->getServices(false);

        //  get array of ServiceInterestGradeDB objects from PortersDB entity
        $serviceGrades = $entity_->getServiceInterestGrades();

        //  start rebuilding service grades with an ArrayCollection
        $entity_->setServiceInterestGrades(new ArrayCollection());

        //  add each service grade back
        foreach ($serviceGrades as $serviceGrade) {
            $serviceGrade->setService($this->getServiceById($dbServices, $serviceGrade->getServiceId()));
            $entity_->addServiceInterestGrade($serviceGrade);
        }
    }

    //========================================================================================
    //  Helper
    //========================================================================================

    protected function getServiceById($services_, $id_) {
        foreach ($services_ as $service) {
            if ($service->getId() === $id_)
                return $service;
        }
        return null;
    }

}