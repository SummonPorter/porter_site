<?php

// src/AppBundle/Utilities/FormHandler/UpdateFormHandler.php
namespace AppBundle\Utilities\FormHandler;

use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;

class UpdateFormHandler extends FormHandler {

	//	builds the form and returns it
	public function buildForm(CmsOperations $cmsOperations_, Array $entities_ = null) {
		//  create new EntitiesList
        $eList = new EntitiesList('update');

        foreach ($entities_ as $entity)
        	$eList->addEntity($entity);

        return $cmsOperations_->getFormFactory()->create($this->getFormClass(), $eList, ['action' => $cmsOperations_->getSourceRoute(), 'method' => 'POST']);
	}

	//	processes submit and returns a redirect response if needed
	//	may also create and add new flash messages to session
	public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
		$entityList = $results_['data'];
        
        //  inserting into the database
        if ($entityList->getListFunction() === 'update') {

            //  running pre update ops
            foreach ($entityList->getEntities() as $e)
                $this->preUpdateOps($cmsOperations_, $request_, $e);

            $cmsOperations_->getDatabase()->updateEntities($entityList->getEntities(), true, true);

            //  running post update ops
            foreach ($entityList->getEntities() as $e)
                $this->postUpdateOps($cmsOperations_, $request_, $e);

            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the update operation was successfully executed.');
            return $this->generateUrl($cmsOperations_, $request_)->send();
        }

        return null;
	}

    //  called before updating an entity to perform any special operations that may be necessary for extending classes
    protected function preUpdateOps(CmsOperations $cmsOperations_, Request $request_, $entity_) {
    }

    //  called after updating an entity to perform any special operations that may be necessary for extending classes
    protected function postUpdateOps(CmsOperations $cmsOperations_, Request $request_, $entity_) {
    }

}