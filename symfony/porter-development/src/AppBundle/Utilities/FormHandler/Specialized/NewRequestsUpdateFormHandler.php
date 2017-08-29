<?php

// src/AppBundle/Utilities/FormHandler/Specialized/NewRequestsUpdateFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\PortersDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Utilities\FormHandler\UpdateFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

class NewRequestsUpdateFormHandler extends UpdateFormHandler {

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
                $request = $cmsOperations_->getDatabase()->getRequest($requestForm->getRId());
                
                //  updating Request with form data
                $request->setService($requestForm->getRService());
                $request->setDiscount($requestForm->getRDiscount());
                $request->setPortersWanted($requestForm->getRPortersWanted());
                $request->setRequestStartDateTime($requestForm->getRStartDateTime());
                $request->setEstDuration($requestForm->getREstDuration());
                $request->setDetails($requestForm->getRDetails());

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
                    $request->addAddress($dbAddress);
                }

                $requestUpdate[] = $request;
            }

            $cmsOperations_->getDatabase()->updateEntities($requestUpdate, true, true);

            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the update operation was successfully executed.');
            return $this->generateUrl($cmsOperations_, $request_)->send();
        }

        return null;
    }
    
}