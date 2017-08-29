<?php

// src/AppBundle/Utilities/FormHandler/Specialized/PortersInsertFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Utilities\FormHandler\InsertFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class PortersInsertFormHandler extends InsertFormHandler {

	//========================================================================================
    //  Overridden
    //========================================================================================

    //  called before inserting an entity to perform any special operations that may be necessary for extending classes
    protected function preInsertOps(CmsOperations $cmsOperations_, Request $request_, $entity_) {
        //  generate salt for the porter
        $porterToken = $cmsOperations_->getSecurityOps()->getPorterToken();
        $entity_->setIdToken($porterToken);

        //  loop through vehicle access, generate warnings and ignore values if settings are not both configured
        $vehicles = $entity_->getVehicles();
        $nVehicles = new ArrayCollection();
        foreach ($vehicles as $access) {
            if ($access->getVehicleType() !== null && $access->getPersonal() !== null)
                $nVehicles->add($access);
            elseif ($access->getVehicleType() !== null || $access->getPersonal() !== null)
                $this->addWarningMessage($cmsOperations_, $request_->getSession(), 'a vehicle access property was not configured properly, and was therefore not saved.');
        }
        $entity_->setVehicles($nVehicles);

        //  get services
        $dbServices = $cmsOperations_->getDatabase()->getServices(false);

        //	configure ServiceInterestGradeDB objects on the PortersDB object
        foreach ($entity_->getServiceInterestGrades() as $serviceInterest)
        	$serviceInterest->setService($this->getServiceById($dbServices, $serviceInterest->getServiceId()));
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