<?php

// src/AppBundle/Utilities/FormHandler/Specialized/ServiceGroupsMainFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Utilities\FormHandler\MainFormHandler;
use AppBundle\Services\CmsOperations;
use Symfony\Component\HttpFoundation\Request;

class ServiceGroupsMainFormHandler extends MainFormHandler {

	//  called to see if entity is safe to delete, returns false if it is not safe, returns true if it is
    //  override in extending classes to implement special functionality - should add flash message if returning false
    protected function canDelete(CmsOperations $cmsOperations_, Request $request_, int $entityId_): bool {
        $services = $cmsOperations_->getDatabase()->getServicesForServiceGroup($entityId_, true);

        if (!empty($services)) {
            $errorMessage = 'the delete operation could not be performed: Service Group #' . $entityId_ . ' is referenced by ' . count($services);
            if (count($services) > 1)
                $errorMessage .= ' other Services.';
            else
                $errorMessage .= ' other Service.';

            $this->addErrorMessage($cmsOperations_, $request_->getSession(), $errorMessage);
            return false;
        }

        return true;
    }

}