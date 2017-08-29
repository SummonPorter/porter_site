<?php

// src/AppBundle/Utilities/FormHandler/Specialized/ServicesMainFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Utilities\FormHandler\MainFormHandler;
use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Collections\ArrayCollection;

class ServicesMainFormHandler extends MainFormHandler {

	//  called to see if entity is safe to delete, returns false if it is not safe, returns true if it is
    //  override in extending classes to implement special functionality - should add flash message if returning false
    protected function canDelete(CmsOperations $cmsOperations_, Request $request_, int $entityId_): bool {
        $serviceReferenceCount = $cmsOperations_->getDatabase()->getServicesReferenceCount($entityId_, true);
        
        if ($serviceReferenceCount !== 0) {
            $errorMessage = 'the delete operation could not be performed: Service #' . $entityId_ . ' is referenced by ' . $serviceReferenceCount;
            if ($serviceReferenceCount > 1)
                $errorMessage .= ' other Requests.';
            else
                $errorMessage .= ' other Request.';

            $this->addErrorMessage($cmsOperations_, $request_->getSession(), $errorMessage);
            return false;
        }

        return true;
    }

    //  called before deleting an entity to perform any special operations that may be necessary for extending classes
    protected function preDeleteOps(CmsOperations $cmsOperations_, Request $request_, int $entityId_) {

        //  get all PortersDB entities
        $dbPorters = $cmsOperations_->getDatabase()->getPorters(false);
        //  service interests to remove
        $removingServiceGrades = [];
        //  for each PortersDB, remove the associated ServiceInterestGradeDB entity
        foreach ($dbPorters as $porter) {
            $interests = $porter->getServiceInterestGrades();

            //  loop through all interests, remove the one that matches the deleted service
            foreach ($interests as $interest) {
                if ($interest->getService()->getId() === $entityId_) {
                    $cmsOperations_->getLogger()->debug('found service ' . $entityId_ . ' for porter ' . $porter->getId());
                    $porter->removeServiceInterestGrade($interest);
                    break;
                }
            }
        }
    }

}