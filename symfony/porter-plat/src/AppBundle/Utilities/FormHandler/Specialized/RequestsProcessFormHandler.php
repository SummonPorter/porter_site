<?php

// src/AppBundle/Utilities/FormHandler/Specialized/RequestsProcessFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Entity\CustomersDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Services\BillingOperations;
use AppBundle\Form\Request\RequestsProcessType;
use AppBundle\Utilities\FormHandler\FormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RequestsProcessFormHandler extends FormHandler {

	//	builds the form and returns it
	public function buildForm(CmsOperations $cmsOperations_, Array $entities_ = null) {
		return $cmsOperations_->getFormFactory()->create($this->getFormClass(), $entities_[0], ['action' => $cmsOperations_->getSourceRoute(), 'method' => 'POST']);
	}

	//	processes submit and returns a redirect response if needed
	//	may also create and add new flash messages to session
	public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
		$request = $cmsOperations_->getDatabase()->getRequest($results_['data']->getRequestId());
		$request->setRequestState($results_['data']->getNewState());

		switch ($results_['data']->getNewState()) {
			case 'active':
				$request->setIdToken($cmsOperations_->getSecurityOps()->getRequestToken());
				//	sending out those emails!
				$cmsOperations_->getRequestOps()->alertPortersAboutNewRequest($request);
				$this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the request has been successfully moved to "active".');
				break;
			case 'worked':
				$request->setIdToken(null);
				$request = $cmsOperations_->getRequestOps()->updateRequestNumbers($request);
				$this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the request has been successfully moved to "worked".');
				break;
			case 'complete':
				$request->setIdToken(null);

				//	billing customer
				if ($cmsOperations_->getBillingOps()->billCustomer($request->getCustomer()->getEmail(), $request->getBillingTotal())) {
					$this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the customer has been successfully billed - request is now "complete".');
					break;
				}
				else {
					$request->setRequestState('worked');
					$this->addErrorMessage($cmsOperations_, $request_->getSession(), 'there was an error during billing - check payfirma dashboard.');
					break;
				}
		}
		
		$cmsOperations_->getDatabase()->updateEntities([$request], true, true);
        return $this->generateUrl($cmsOperations_, $request_)->send();
	}

}