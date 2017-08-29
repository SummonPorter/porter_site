<?php

// src/AppBundle/Utilities/FormHandler/MainFormHandler.php
namespace AppBundle\Utilities\FormHandler;

use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;

class MainFormHandler extends FormHandler {

	//	builds the form and returns it
	public function buildForm(CmsOperations $cmsOperations_, Array $entities_ = null) {
		//  create new EntitiesList
        $eList = new EntitiesList('delete');

        foreach ($entities_ as $entity)
            $eList->addEntity($entity);

        return $cmsOperations_->getFormFactory()->create($this->getFormClass(), $eList, ['action' => $cmsOperations_->getSourceRoute(), 'method' => 'POST']);
	}

	//	processes submit and returns a redirect response if needed
	//	may also create and add new flash messages to session
	public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_) {
		$entityList = $results_['data'];

        //  safeguard - only continue if list function matches
        if ($entityList->getListFunction() === 'delete') {
            //  CheckedEntity objects array
            $entities = $entityList->getEntities();
            $checkedIds = [];
            $checkedPath;
            //  loop through all entities, and operate only on the ones that have been checked
            foreach ($entities as $e) {
                if ($e->getChecked()) {
                    if (!$this->canDelete($cmsOperations_, $request_, $e->getId()))
                        return null;
                    $checkedIds[] = $e->getId();
                    $checkedPath = $e->getDoctrinePath();
                }
            }

            //  deleting the entities
            if (!empty($checkedIds)) {
                //  performing any necessary special functions
                foreach ($checkedIds as $e)
                    $this->preDeleteOps($cmsOperations_, $request_, $e);

                $cmsOperations_->getDatabase()->deleteIdsIn($checkedIds, $checkedPath, true, true);
            }

            $this->addSuccessMessage($cmsOperations_, $request_->getSession(), 'the delete operation was successfully executed.');
            return $this->generateUrl($cmsOperations_, $request_)->send();
        }

        return null;
	}

    //  called to see if entity is safe to delete, returns false if it is not safe, returns true if it is
    //  override in extending classes to implement special functionality - should add flash message if returning false
    protected function canDelete(CmsOperations $cmsOperations_, Request $request_, int $entityId_): bool {
        return true;
    }

    //  called before deleting an entity to perform any special operations that may be necessary for extending classes
    protected function preDeleteOps(CmsOperations $cmsOperations_, Request $request_, int $entityId_) {
    }

}