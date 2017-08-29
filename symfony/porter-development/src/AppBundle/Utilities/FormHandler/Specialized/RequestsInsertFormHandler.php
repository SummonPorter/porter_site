<?php

// src/AppBundle/Utilities/FormHandler/Specialized/RequestsInsertFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CustomersDB;
use AppBundle\Entity\RequestAddressesDB;
use AppBundle\Utilities\FormHandler\InsertFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;

class RequestsInsertFormHandler extends InsertFormHandler {

	//========================================================================================
    //  Overridden
    //========================================================================================

	//	CustomersDB and RequestsDB objects must be created from the NewRequestEntity object
    public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
		$entityList = $results_['data'];
        $defaultCity = $cmsOperations_->getDatabase()->getDefaultCity();
        
        //  inserting into the database
        if ($entityList->getListFunction() === 'insert') {

        	$newRequests = [];

        	foreach ($entityList->getEntities() as $requestForm) {
        		//	validating credit card information
        		
        		//	building Customer
        		$customer = new CustomersDB();
        		$customer->setGuest(true);
        		$customer->setEmail($requestForm->getCEmail());
        		$customer->setFirstName($requestForm->getCFirstName());
        		$customer->setLastName($requestForm->getCLastName());
        		$customer->setMobilePhone($requestForm->getCMobilePhone());
        		$customer->setHomePhone($requestForm->getCHomePhone());
        		$customer->setCity($requestForm->getCCity());
        		$customer->setAddress($requestForm->getCAddress());
        		$customer->setMailing($requestForm->getCMailing());

                //  building Request
        		$request = new RequestsDB();
                $request->setCustomer($customer);
                $request->setIdToken(null);
                $request->setService($requestForm->getRService());
                $request->setDiscount($requestForm->getRDiscount());
                $request->setPortersWanted($requestForm->getRPortersWanted());
                $request->setRequestStartDateTime($requestForm->getRStartDateTime());
                $request->setEstDuration($requestForm->getREstDuration());
                $request->setDetails($requestForm->getRDetails());
                
                //  HACK: due to design, cannot define the addresses when creating the initial request from the backend as
                //  it depends upon the selected service (# of addresses) - so here after the fact the correct number of addresses
                //  are created and added to the new request object which then need updating after the fact

                for ($i = 0; $i < $request->getService()->getAddressCount(); $i++) {
                    $address = new RequestAddressesDB();
                    $address->setCity($defaultCity);
                    $address->setAddress('address');
                    $address->setMailing('mailing');
                    $address->setUnit('unit');
                    $address->setContext('context');
                    $address->setStepping($i);
                    $request->addAddress($address);
                }

                //  standard Request setup
                $request->setRequestState('new');
                $request->setRequestEpoch(time());

                $newRequests[] = $request;
        	}

            //  inserting
            $cmsOperations_->getDatabase()->addEntities($newRequests, false, true);

            //  TODO: performing business logic for when a new request is received

            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the insert operation was successfully executed.');
        	return $this->generateUrl($cmsOperations_, $request_)->send();
    	}

    	return null;
	}

}