<?php

// src/AppBundle/Utilities/FormHandler/Specialized/RegionsMainFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Utilities\FormHandler\MainFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;

class RegionsMainFormHandler extends MainFormHandler {

	//  called to see if entity is safe to delete, returns false if it is not safe, returns true if it is
    //  override in extending classes to implement special functionality - should add flash message if returning false
    protected function canDelete(CmsOperations $cmsOperations_, Request $request_, int $entityId_): bool {
        $cities = $cmsOperations_->getDatabase()->getCitiesForRegion($entityId_, true);

        if (!empty($cities)) {
            $errorMessage = 'the delete operation could not be performed: Region #' . $entityId_ . ' is referenced by ' . count($cities);
            if (count($cities) > 1)
                $errorMessage .= ' other Cities.';
            else
                $errorMessage .= ' other City.';

            $this->addErrorMessage($cmsOperations_, $request_->getSession(), $errorMessage);
            return false;
        }

        return true;
    }

}