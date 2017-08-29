<?php

// src/AppBundle/Utilities/FormHandler/Specialized/ServicesInsertFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Utilities\FormHandler\InsertFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class ServicesInsertFormHandler extends InsertFormHandler {

	//========================================================================================
    //  Overridden
    //========================================================================================

    //  processes submit and returns a redirect response if needed
    //  may also create and add new flash messages to session
    public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
        $entityList = $results_['data'];
        
        //  inserting into the database
        if ($entityList->getListFunction() === 'insert') {

            //  running pre insert ops
            foreach ($entityList->getEntities() as $e)
                $this->preInsertOps($cmsOperations_, $request_, $e);

            //  inserting
            $cmsOperations_->getDatabase()->addEntities($entityList->getEntities(), false, true);

            //  running post insert ops, but on all services to ensure that all porters have a service interest grade
            $allServices = $cmsOperations_->getDatabase()->getServices(false);
            foreach ($allServices as $e)
                $this->postInsertOps($cmsOperations_, $request_, $e);

            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the insert operation was successfully executed.');
            return $this->generateUrl($cmsOperations_, $request_)->send();
        }

        return null;
    }

    //  called before inserting an entity to perform any special operations that may be necessary for extending classes
    //  new ServiceInterestGradeDB entities must be created for each porter when a new services is created
    protected function postInsertOps(CmsOperations $cmsOperations_, Request $request_, $entity_) {

        //  list of updated porters
        $updatedPorters = [];
        //  get all PortersDB entities
        $dbPorters = $cmsOperations_->getDatabase()->getPorters(false);
        //  for each PortersDB, create a new ServiceInterestGradeDB for the service if needed
        foreach ($dbPorters as $porter) {
            //  checkingn to see if the porter needs the service
            $pService = $porter->getInterestGradeForService($entity_);
            //  if porter doesn't have that service, build the necessary interest grade
            if ($pService === null) {
                $interest = new ServiceInterestGradeDB();
                $interest->setInterestGrade(2);
                $interest->setService($entity_);
                $porter->addServiceInterestGrade($interest);
                $updatedPorters[] = $porter;
            }
        }

        $cmsOperations_->getDatabase()->updateEntities($updatedPorters, false, true);
    }

}