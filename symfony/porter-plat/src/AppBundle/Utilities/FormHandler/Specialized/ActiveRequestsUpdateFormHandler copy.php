<?php

// src/AppBundle/Utilities/FormHandler/Specialized/ActiveRequestsUpdateFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PorterAssignmentsDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Utilities\FormHandler\UpdateFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class ActiveRequestsUpdateFormHandler extends UpdateFormHandler {

	//========================================================================================
    //  Overridden
    //========================================================================================

    //  processes submit and returns a redirect response if needed
    //  may also create and add new flash messages to session
    public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
        $entityList = $results_['data'];
        
        //  inserting into the database
        if ($entityList->getListFunction() === 'update') {

            $requestUpdate = [];

            foreach ($entityList->getEntities() as $requestForm) {
                //  load request by ID
                $requestEntity = $cmsOperations_->getDatabase()->getRequest($requestForm->getRId());
                
                //  updating Request with form data
                $requestEntity->setDiscount($requestForm->getRDiscount());
                $requestEntity->setPortersWanted($requestForm->getRPortersWanted());
                $requestEntity->setRequestStartEpoch($requestForm->getRStartEpoch());
                $requestEntity->setEstDuration($requestForm->getREstDuration());
                $requestEntity->setDetails($requestForm->getRDetails());
                $requestEntity->setAddress($requestForm->getRAddress());

                //  getting list of porters from form
                $newPorterList = $this->getNewPorterList($cmsOperations_, $request_, $requestForm);
                //  ensures that any porters not in the new list from the request are unassigned
                $requestEntity = $this->unassignMissingPorters($cmsOperations_, $request_, $requestEntity, $newPorterList);
                //  ensure the new list is composed entirely of only new, unassigned porters
                $newPorterList = $this->removeExistingAssignments($cmsOperations_, $requestEntity, $newPorterList);

                //  TODO: perform logic on new porters that are going to be assigned (send emails, etc)

                //  create new PorterAssignmentsDB objects and add them to the request
                $requestEntity = $this->updateRequestForAssignments($cmsOperations_, $request_, $requestEntity, $newPorterList);

                $requestUpdate[] = $requestEntity;
            }

            $cmsOperations_->getDatabase()->updateEntities($requestUpdate, true, true);

            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the update operation was successfully executed.');
            return $this->generateUrl($cmsOperations_, $request_)->send();
        }

        return null;
    }

    //========================================================================================
    //  Helpers
    //========================================================================================

    private function getNewPorterList(CmsOperations $cmsOperations_, Request $request_, $requestForm_) {
        $newPorterIdList = [];

        //  grab the porter ids
        foreach ($requestForm_->getRPorterAssignments() as $porterId) {
            if ($porterId !== null)
                $newPorterIdList[] = intval($porterId);
        }

        //  remove duplicate ids
        $dups = false;
        for ($i = 0; $i < count($newPorterIdList); $i++) {
            for ($c = $i + 1; $c < count($newPorterIdList); $c++) {
                //  remove duplicates
                if ($newPorterIdList[$i] === $newPorterIdList[$c]) {
                    $dups = true;
                    unset($newPorterIdList[$c]);
                    $newPorterIdList = array_values($newPorterIdList);
                    $c--;
                }
            }
        }

        //  warning message
        if ($dups === true)
            $this->addWarningMessage($cmsOperations_, $request_->getSession(), 'duplicate Porters were detected for assignment and removed.');

        //  prep array for query
        $newPorterIdList = implode(',', $newPorterIdList);
        //  now load porters from database
        $em = $cmsOperations_->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:PortersDB', 'p')
            ->where($qb->expr()->in('p.id', '\'' . $newPorterIdList . '\''));

        return $qb->getQuery()->getResult();
    }

    //  TODO: use time data to prevent removing porters that were assigned while user was in request form
    private function unassignMissingPorters(CmsOperations $cmsOperations_, Request $request_, RequestsDB $requestEntity_, $newPorterList_): RequestsDB {
        $existingAssignments = $requestEntity_->getPorterAssignments();
        $keptAssignments = [];

        foreach ($existingAssignments as $assignment) {
            if ($this->hasPorter($newPorterList_, $assignment->getPorter()) === false) {
                $this->addWarningMessage($cmsOperations_, $request_->getSession(), 'Porter ' . $assignment->getPorter()->getFullName() . ' has been un-assigned from the request.');
                $cmsOperations_->getDatabase()->deleteEntities([$assignment]);
            }
            else
                $keptAssignments[] = $assignment;
        }

        //  building new ArrayCollection of assignments that aren't to be removed
        $refinedCollection = new ArrayCollection();
        foreach ($keptAssignments as $assignment)
            $refinedCollection->add($assignment);
        $requestEntity_->setPorterAssignments($refinedCollection);

        return $requestEntity_;
    }

    //  returns updated list with any already assigned porters removed from the new list
    private function removeExistingAssignments(CmsOperations $cmsOperations_, $requestEntity_, $newPorterList_): Array {
        $updatedList = [];

        foreach ($newPorterList_ as $porter) {
            if (!$requestEntity_->hasPorterAssigned($porter))
                $updatedList[] = $porter;
        }

        return $updatedList;
    }

    //  returns false if list_ does not have porter_
    private function hasPorter($list_, $porter_): bool {
        foreach ($list_ as $comparer) {
            if ($comparer->getId() === $porter_->getId())
                return true;
        }

        return false;
    }

    //  creates new assignments for the new porters
    private function updateRequestForAssignments(CmsOperations $cmsOperations_, Request $request_, RequestsDB $requestEntity_, $newPorterList_): RequestsDB {
        foreach ($newPorterList_ as $porter) {
            $newAssignment = new PorterAssignmentsDB();
            $newAssignment->setPorter($porter);
            $newAssignment->setAssignmentEpoch(round(microtime(true) * 1000));

            $requestEntity_->addPorterAssignment($newAssignment);
            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'Porter ' . $porter->getFullName() . ' has been assigned to the request.');
        }

        return $requestEntity_;
    }
    
}