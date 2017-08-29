<?php

// src/AppBundle/Utilities/FormHandler/MainInsertFormHandler.php
namespace AppBundle\Utilities\FormHandler;

use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;

class MainInsertFormHandler extends FormHandler {

	//	builds the form and returns it
	public function buildForm(CmsOperations $cmsOperations_, Array $entities_ = null) {
		return $cmsOperations_->getFormFactory()->create($this->getFormClass(), null, ['action' => $cmsOperations_->getSourceRoute(), 'method' => 'POST']);
	}

	//	processes submit and returns a redirect response if needed
	//	may also create and add new flash messages to session
	public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
		$count = $results_['data']->getInsert();
		
		$this->getRedirectPath()->addRouteParameter('count_', $count);
        return $this->generateUrl($cmsOperations_, $request_)->send();
	}

}