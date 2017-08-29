<?php

// src/AppBundle/Utilities/FormHandler/Specialized/RequestsUpdateFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PorterAssignmentsDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Utilities\FormHandler\UpdateFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class RequestsUpdateFormHandler extends UpdateFormHandler {

    //========================================================================================
    //  Helpers
    //========================================================================================

    //  TODO: use time data to prevent removing porters that were assigned while user was in request form
    //  iterates through the new porter list, and unassigns any porters that were previously assigned to the request that aren't on the new list
    protected function unassignMissingPorters(CmsOperations $cmsOperations_, Request $request_, RequestsDB $requestEntity_, $newPorterList_): RequestsDB {
        $existingAssignments = $requestEntity_->getPorterAssignments();
        $keptAssignments = [];

        foreach ($existingAssignments as $assignment) {
            //  porter from assignment
            $porter = $assignment->getPorter();

            if ($this->hasPorter($newPorterList_, $porter) === false) {
                $this->addWarningMessage($cmsOperations_, $request_->getSession(), 'Porter ' . $assignment->getPorter()->getFullName() . ' has been un-assigned from the request.');
                //  remove the assignment, and all activities, get updated request object
                $requestEntity_ = $cmsOperations_->getRequestOps()->removeExistingAssignment($requestEntity_, $porter);
            }
        }

        return $requestEntity_;
    }

    //  returns updated list with any already assigned porters removed from the new list
    protected function removeExistingAssignments(CmsOperations $cmsOperations_, $requestEntity_, $newPorterList_): Array {
        $updatedList = [];

        foreach ($newPorterList_ as $porter) {
            if (!$requestEntity_->hasPorterAssigned($porter))
                $updatedList[] = $porter;
        }

        return $updatedList;
    }

    //  returns false if list_ does not have porter_
    protected function hasPorter($list_, $porter_): bool {
        foreach ($list_ as $comparer) {
            if ($comparer->getId() === $porter_->getId())
                return true;
        }

        return false;
    }

    //  creates new assignments for the new porters
    protected function updateRequestForAssignments(CmsOperations $cmsOperations_, Request $request_, RequestsDB $requestEntity_, $newPorterList_): RequestsDB {
        foreach ($newPorterList_ as $porter) {
            //  creating new assignment via request operations
            if ($cmsOperations_->getRequestOps()->addNewAssignment($requestEntity_, $porter) === true)
                $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'Porter ' . $porter->getFullName() . ' has been assigned to the request.');
            else
                $this->addErrorMessage($cmsOperations_, $request_->getSession(), 'Porter ' . $porter->getFullName() . ' was unable to be assigned to the request.');
        }

        return $requestEntity_;
    }
    
}