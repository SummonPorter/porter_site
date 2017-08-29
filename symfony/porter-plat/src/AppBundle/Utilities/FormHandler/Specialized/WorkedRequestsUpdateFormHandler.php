<?php

// src/AppBundle/Utilities/FormHandler/Specialized/WorkedRequestsUpdateFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PorterAssignmentsDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Entity\WorkedUpdateRequestEntity;
use AppBundle\Utilities\FormHandler\Specialized\RequestsUpdateFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class WorkedRequestsUpdateFormHandler extends RequestsUpdateFormHandler {

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
            $activityUpdates = [];

            //  loop through every WorkedUpdateRequestEntity
            foreach ($entityList->getEntities() as $requestForm) {
                //  load request by ID
                $requestEntity = $cmsOperations_->getDatabase()->getRequest($requestForm->getRId());

                //  updating Request with form data
                $requestEntity->setDiscount($requestForm->getRDiscount());

                //  getting list of porters from form
                $newPorterList = $this->getNewPorterList($cmsOperations_, $request_, $requestForm);

                //  ensures that any porters not in the new list from the request are unassigned
                $requestEntity = $this->unassignMissingPorters($cmsOperations_, $request_, $requestEntity, $newPorterList);
                //  ensure the new list is composed entirely of only new, unassigned porters
                $newPorterList = $this->removeExistingAssignments($cmsOperations_, $requestEntity, $newPorterList);

                //  create new PorterAssignmentsDB objects and add them to the request
                $requestEntity = $this->updateRequestForAssignments($cmsOperations_, $request_, $requestEntity, $newPorterList);
                $requestUpdate[] = $requestEntity;

                $assignedPorters = $requestEntity->getPorterAssignments();

                //  work on activity data for all remaining porters to update/create them
                $porterSummaries = $requestForm->getPorterSummaries();
                foreach ($porterSummaries as $porterSummary) {
                    //  comparing against the porters
                    if ($porterSummary->getPorter() !== null) {
                        foreach ($assignedPorters as $porterAssignment) {
                            if ($porterSummary->getPorter()->getId() === $porterAssignment->getPorter()->getId()) {
                                $activity = $porterSummary->getStartActivity();
                                $activity->setPorter($porterSummary->getPorter());
                                $activity->setRequest($requestEntity);
                                $activityUpdates[] = $activity;
                                $activity = $porterSummary->getEndActivity();
                                $activity->setPorter($porterSummary->getPorter());
                                $activity->setRequest($requestEntity);
                                $activityUpdates[] = $activity;
                            }
                        }
                    }
                }
            }

            $cmsOperations_->getDatabase()->updateEntities($activityUpdates, true, true);

            //  update request
            foreach ($requestUpdate as &$requestEU)
                $requestEU = $cmsOperations_->getRequestOps()->updateRequestNumbers($requestEU);
            
            $cmsOperations_->getDatabase()->updateEntities($requestUpdate, true, true);
            
            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the update operation was successfully executed.');
            return $this->generateUrl($cmsOperations_, $request_)->send();
        }

        return null;
    }

    //========================================================================================
    //  Helpers
    //========================================================================================

    //  returns PortersDB entity objects for all the Porter ids supplied on update
    protected function getNewPorterList(CmsOperations $cmsOperations_, Request $request_, $requestForm_) {
        $porterSummaries = $requestForm_->getPorterSummaries();
        $newPorterIdList = [];

        //  grab the ids from the porter objects
        foreach ($porterSummaries as $porterSummary) {
            if ($porterSummary->getPorter() !== null)
                $newPorterIdList[] = $porterSummary->getPorter()->getId();
        }

        //  if the array is empty, just skip all this
        if (empty($newPorterIdList) === true)
            return $newPorterIdList;

        //  remove duplicate ids
        $uniqueList = $cmsOperations_->getMathOps()->removeDuplicates($newPorterIdList);

        //  warning message, if there were duplicates
        if (count($newPorterIdList) !== count($uniqueList))
            $this->addWarningMessage($cmsOperations_, $request_->getSession(), 'duplicate Porters were detected for assignment and removed.');

        //  if the unique array is empty, just return
        if (empty($uniqueList) === true)
            return $uniqueList;

        //  lastly, use the remaining ids as a whitelist against the original list of porters
        $filteredPorters = [];
        foreach ($porterSummaries as $porterSummary) {
            if ($porterSummary->getPorter() !== null) {
                foreach ($uniqueList as $porterId) {
                    if ($porterSummary->getPorter()->getId() === $porterId)
                        $filteredPorters[] = $porterSummary->getPorter();
                }
            }
        }

        return $filteredPorters;
    }
    
}