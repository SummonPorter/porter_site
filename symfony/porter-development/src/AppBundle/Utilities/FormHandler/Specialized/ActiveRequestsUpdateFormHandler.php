<?php

// src/AppBundle/Utilities/FormHandler/Specialized/ActiveRequestsUpdateFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PorterAssignmentsDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Utilities\FormHandler\Specialized\RequestsUpdateFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class ActiveRequestsUpdateFormHandler extends RequestsUpdateFormHandler {

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
                $requestEntity->setRequestStartDateTime($requestForm->getRStartDateTime());
                $requestEntity->setEstDuration($requestForm->getREstDuration());
                $requestEntity->setDetails($requestForm->getRDetails());

                //  getting list of porters from form
                $newPorterList = $this->getNewPorterList($cmsOperations_, $request_, $requestForm);

                //  ensures that any porters not in the new list from the request are unassigned
                $requestEntity = $this->unassignMissingPorters($cmsOperations_, $request_, $requestEntity, $newPorterList);
                //  ensure the new list is composed entirely of only new, unassigned porters
                $newPorterList = $this->removeExistingAssignments($cmsOperations_, $requestEntity, $newPorterList);
                
                //  create new PorterAssignmentsDB objects and add them to the request
                $requestEntity = $this->updateRequestForAssignments($cmsOperations_, $request_, $requestEntity, $newPorterList);

                //  adding addresses to request object
                $addresses = $requestForm->getRAddresses();
                foreach ($addresses as $address) {
                    //  get address from doctrine
                    $dbAddress = $cmsOperations_->getDatabase()->getRequestAddress($address->getId());
                    //  update address
                    $dbAddress->setCity($address->getCity());
                    $dbAddress->setAddress($address->getAddress());
                    $dbAddress->setMailing($address->getMailing());
                    $dbAddress->setUnit($address->getUnit());
                    $dbAddress->setContext($address->getContext());
                    $dbAddress->setStepping($address->getStepping());
                    //  adding address to request
                    $requestEntity->addAddress($dbAddress);
                }

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

    //  returns PortersDB entity objects for all the Porter ids supplied on update
    protected function getNewPorterList(CmsOperations $cmsOperations_, Request $request_, $requestForm_) {
        $newPorterIdList = [];

        //  grab the porter ids
        foreach ($requestForm_->getRPorterAssignments() as $porterId) {
            if ($porterId !== null)
                $newPorterIdList[] = intval($porterId);
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

        //  prep array for query
        $uniqueList = implode(',', $uniqueList);
        
        //  now load porters from database
        $em = $cmsOperations_->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:PortersDB', 'p')
            ->where($qb->expr()->in('p.id', $uniqueList));

        return $qb->getQuery()->getResult();
    }
    
}