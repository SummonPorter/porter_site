<?php

// src/AppBundle/Utilities/FormHandler/InsertFormHandler.php
namespace AppBundle\Utilities\FormHandler;

use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;

class InsertFormHandler extends FormHandler {

	//	builds the form and returns it
	public function buildForm(CmsOperations $cmsOperations_, Array $entities_ = null) {
		//  create new EntitiesList
        $eList = new EntitiesList('insert');

        foreach ($entities_ as $entity)
        	$eList->addEntity($entity);

        return $cmsOperations_->getFormFactory()->create($this->getFormClass(), $eList, ['action' => $cmsOperations_->getSourceRoute(), 'method' => 'POST']);
	}

	//	processes submit and returns a redirect response if needed
	//	may also create and add new flash messages to session
	public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
		$entityList = $results_['data'];
        
        //  inserting into the database
        if ($entityList->getListFunction() === 'insert') {

            //  running pre insert ops
            foreach ($entityList->getEntities() as $e)
                $this->preInsertOps($cmsOperations_, $request_, $e);

            //  inserting
            $cmsOperations_->getDatabase()->addEntities($entityList->getEntities(), false, true);

            //  running post insert ops
            foreach ($entityList->getEntities() as $e)
                $this->postInsertOps($cmsOperations_, $request_, $e);

            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the insert operation was successfully executed.');
        	return $this->generateUrl($cmsOperations_, $request_)->send();
    	}

    	return null;
	}

    //  called before inserting an entity to perform any special operations that may be necessary for extending classes
    protected function preInsertOps(CmsOperations $cmsOperations_, Request $request_, $entity_) {
    }

    //  called before inserting an entity to perform any special operations that may be necessary for extending classes
    protected function postInsertOps(CmsOperations $cmsOperations_, Request $request_, $entity_) {
    }

}