<?php

// src/AppBundle/Services/FormHandlerFactory.php
namespace AppBundle\Services;

use AppBundle\Utilities\Misc\RoutePath;
use AppBundle\Utilities\FormHandler\FormHandler;
use AppBundle\Utilities\FormHandler\MainFormHandler;
use AppBundle\Utilities\FormHandler\MainInsertFormHandler;
use AppBundle\Utilities\FormHandler\InsertFormHandler;
use AppBundle\Utilities\FormHandler\UpdateFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\CountriesMainFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\RegionsMainFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\ServiceGroupsMainFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\PortersInsertFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\PortersUpdateFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\CitiesMainFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\RequestsInsertFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\RequestsProcessFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\NewRequestsUpdateFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\ActiveRequestsUpdateFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\WorkedRequestsUpdateFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\ServicesMainFormHandler;
use AppBundle\Utilities\FormHandler\Specialized\ServicesInsertFormHandler;

class FormHandlerFactory {

	public static function getHandler(string $handlerType_, string $formClass_, RoutePath $redirectPath_): FormHandler {
		switch ($handlerType_) {
			case 'main_form':
				return new MainFormHandler($formClass_, $redirectPath_);
			case 'main_insert_form':
				return new MainInsertFormHandler($formClass_, $redirectPath_);
			case 'insert_form':
				return new InsertFormHandler($formClass_, $redirectPath_);
			case 'update_form':
				return new UpdateFormHandler($formClass_, $redirectPath_);
			case 'countries_main_form':
				return new CountriesMainFormHandler($formClass_, $redirectPath_);
			case 'regions_main_form':
				return new RegionsMainFormHandler($formClass_, $redirectPath_);
			case 'service_groups_main_form':
				return new ServiceGroupsMainFormHandler($formClass_, $redirectPath_);
			case 'porters_insert_form':
				return new PortersInsertFormHandler($formClass_, $redirectPath_);
			case 'porters_update_form':
				return new PortersUpdateFormHandler($formClass_, $redirectPath_);
			case 'cities_main_form':
				return new CitiesMainFormHandler($formClass_, $redirectPath_);
			case 'requests_insert_form':
				return new RequestsInsertFormHandler($formClass_, $redirectPath_);
			case 'requests_process_form':
				return new RequestsProcessFormHandler($formClass_, $redirectPath_);
			case 'requests_new_update_form':
				return new NewRequestsUpdateFormHandler($formClass_, $redirectPath_);
			case 'requests_active_update_form':
				return new ActiveRequestsUpdateFormHandler($formClass_, $redirectPath_);
			case 'requests_worked_update_form':
				return new WorkedRequestsUpdateFormHandler($formClass_, $redirectPath_);
			case 'services_main_form':
				return new ServicesMainFormHandler($formClass_, $redirectPath_);
			case 'services_insert_form':
				return new ServicesInsertFormHandler($formClass_, $redirectPath_);
		}
	}

}