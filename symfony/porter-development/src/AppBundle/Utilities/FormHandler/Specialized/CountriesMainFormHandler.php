<?php

// src/AppBundle/Utilities/FormHandler/Specialized/CountriesMainFormHandler.php
namespace AppBundle\Utilities\FormHandler\Specialized;

use AppBundle\Utilities\FormHandler\MainFormHandler;
use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;

class CountriesMainFormHandler extends MainFormHandler {

	//  called to see if entity is safe to delete, returns false if it is not safe, returns true if it is
    //  override in extending classes to implement special functionality - should add flash message if returning false
    protected function canDelete(CmsOperations $cmsOperations_, Request $request_, int $entityId_): bool {
        $regions = $cmsOperations_->getDatabase()->getRegionsForCountry($entityId_, true);

        if (!empty($regions)) {
            $errorMessage = 'the delete operation could not be performed: Country #' . $entityId_ . ' is referenced by ' . count($regions);
            if (count($regions) > 1)
                $errorMessage .= ' other Regions.';
            else
                $errorMessage .= ' other Region.';

            $this->addErrorMessage($cmsOperations_, $request_->getSession(), $errorMessage);
            return false;
        }

        return true;
    }

}