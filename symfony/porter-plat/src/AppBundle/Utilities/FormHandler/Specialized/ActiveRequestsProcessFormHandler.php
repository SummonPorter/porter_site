<?php

// src/AppBundle/Utilities/FormHandler/Specialized/ActiveRequestsProcessFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Form\Request\NewRequestsProcessType;
use AppBundle\Utilities\FormHandler\FormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ActiveRequestsProcessFormHandler extends FormHandler {

	//	builds the form and returns it
	public function buildForm(CmsOperations $cmsOperations_, Array $entities_ = null) {
		return $cmsOperations_->getFormFactory()->create($this->getFormClass(), $entities_[0], ['action' => $cmsOperations_->getSourceRoute(), 'method' => 'POST']);
	}

	//	processes submit and returns a redirect response if needed
	//	may also create and add new flash messages to session
	public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
		$request = $cmsOperations_->getDatabase()->getRequest($results_['data']->getRequestId());
		$request->setRequestState($results_['data']->getNewState());

		$cmsOperations_->getDatabase()->updateEntities([$request], true, true);

		//	TODO: perform special business logic when changing states of requests
		switch ($results_['data']->getNewState()) {
			case 'active':
				$this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the request has been successfully moved to "worked".');
		}
		
        return $this->generateUrl($cmsOperations_, $request_)->send();
	}

}