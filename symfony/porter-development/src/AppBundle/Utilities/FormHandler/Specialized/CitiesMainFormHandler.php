<?php

// src/AppBundle/Utilities/FormHandler/Specialized/CitiesMainFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Utilities\FormHandler\MainFormHandler;
use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;

class CitiesMainFormHandler extends MainFormHandler {

	//  called to see if entity is safe to delete, returns false if it is not safe, returns true if it is
    //  override in extending classes to implement special functionality - should add flash message if returning false
    protected function canDelete(CmsOperations $cmsOperations_, Request $request_, int $entityId_): bool {
        $cityReferenceCount = $cmsOperations_->getDatabase()->getCitiesReferenceCount($entityId_, true);
        
        if ($cityReferenceCount !== 0) {
            $errorMessage = 'the delete operation could not be performed: City #' . $entityId_ . ' is referenced by ' . $cityReferenceCount;
            if ($cityReferenceCount > 1)
                $errorMessage .= ' other Entities.';
            else
                $errorMessage .= ' other Entity.';

            $this->addErrorMessage($cmsOperations_, $request_->getSession(), $errorMessage);
            return false;
        }

        return true;
    }

}