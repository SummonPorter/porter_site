<?php

// src/AppBundle/Controller/InternalServicesController.php
namespace AppBundle\Controller;

use AppBundle\Entity\CitiesDB;
use AppBundle\Entity\CountriesDB;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\PorterAssignmentsDB;
use AppBundle\Entity\PorterRequestActivityDB;
use AppBundle\Entity\PorterRequestActivitySummary;
use AppBundle\Helpers\LocationsJSON;
use AppBundle\Security\SecurityOperations;
use AppBundle\Utilities\Misc\LocationValidator;
use AppBundle\Utilities\Misc\DiscountValidator;
use AppBundle\Utilities\Misc\SummonProcessor;
use AppBundle\Utilities\Misc\NewCustomerProcessor;
use AppBundle\Utilities\Misc\NewPorterProcessor;
use AppBundle\Utilities\Misc\NewSubscriberProcessor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \DateTime;

class InternalServicesController extends Controller {

	/**
	* @Route("/internal/services/services", name="internal_services_services")
	*/
	public function servicesAction() {
		$services = $this->get('app.database')->getServices(true, false);

		$encoders = array(new JsonEncoder());
		$normalizers = array(new JsonSerializableNormalizer());

		$serializer = new Serializer($normalizers, $encoders);
		$json = $serializer->serialize($services, 'json');

		$response = new Response();
		$response->setContent($json);
		$response->headers->set('Content-Type', 'application/json');
		
		return $response;
	}

	/**
	* @Route("/internal/services/validate/discount", name="internal_services_validate_discount")
	*/
	public function validateDiscountAction(Request $request_) {
		$discountValidator = new DiscountValidator($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$discountValidator->setDoctrine($this->getDoctrine());
		$discountValidator->setDiscountKey('discount_code');
		$discountValidator->setServiceKey('service_id');
		$discountValidator->runValidation();

		$response = new Response();
		$response->setContent($discountValidator->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	* @Route("/internal/services/validate/location", name="internal_services_validate_location")
	*/
	public function validateLocationAction(Request $request_) {
		$locationValidator = new LocationValidator($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$locationValidator->setDoctrine($this->getDoctrine());
		$locationValidator->setLocationKey('location');
		$locationValidator->runValidation();

		$response = new Response();
		$response->setContent($locationValidator->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	* @Route("/internal/services/customer/new", name="internal_services_customer_new")
	*/
	public function customerNewAction(Request $request_) {
		$porterProcessor = new NewCustomerProcessor($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$porterProcessor->setDoctrine($this->getDoctrine());
		$porterProcessor->setDatabase($this->get('app.database'));
		$porterProcessor->runValidation();
		$porterProcessor->performAction();

		$response = new Response();
		$response->setContent($porterProcessor->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	* @Route("/internal/services/customer/login", name="internal_services_customer_login")
	*/
	public function customerLoginAction(Request $request_) {
		/*$porterProcessor = new NewCustomerProcessor($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$porterProcessor->setDoctrine($this->getDoctrine());
		$porterProcessor->setDatabase($this->get('app.database'));
		$porterProcessor->runValidation();
		$porterProcessor->performAction();

		$response = new Response();
		$response->setContent($porterProcessor->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;*/
		return new Response('<html><body>login action</body></html>');
	}

	/**
	* @Route("/internal/services/customer/logout", name="internal_services_customer_logout")
	*/
	public function customerLogoutAction(Request $request_) {
		/*$porterProcessor = new NewCustomerProcessor($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$porterProcessor->setDoctrine($this->getDoctrine());
		$porterProcessor->setDatabase($this->get('app.database'));
		$porterProcessor->runValidation();
		$porterProcessor->performAction();

		$response = new Response();
		$response->setContent($porterProcessor->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;*/
	}

	/**
	* @Route("/internal/services/porter/new", name="internal_services_porter_new")
	*/
	public function porterNewAction(Request $request_) {
		$porterProcessor = new NewPorterProcessor($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$porterProcessor->setDoctrine($this->getDoctrine());
		$porterProcessor->setDatabase($this->get('app.database'));
		$porterProcessor->runValidation();
		$porterProcessor->performAction();

		$response = new Response();
		$response->setContent($porterProcessor->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	* @Route("/internal/services/subscriber/new", name="internal_services_subscriber_new")
	*/
	public function subscriberNewAction(Request $request_) {
		$subscriberProcessor = new NewSubscriberProcessor($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$subscriberProcessor->setMailchimpOps($this->get('app.mailchimp_operations'));
		$subscriberProcessor->runValidation();
		$subscriberProcessor->performAction();

		$response = new Response();
		$response->setContent($subscriberProcessor->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	* @Route("/internal/services/password/{string_}", name="internal_services_password")
	*/
	public function passwordAction(Request $request_, string $string_) {
		return password_hash($string_, PASSWORD_BCRYPT, ['cost' => 14]);
	}
	
	/**
	* @Route("/internal/services/submit/process", name="internal_services_submit_process")
	*/
	public function processSummonSubmit(Request $request_) {
		$summonProcessor = new SummonProcessor($this->get('logger'), $this->get('app.security_operations'), $request_, 'POST');
		$summonProcessor->setDoctrine($this->getDoctrine());
		$summonProcessor->setDatabase($this->get('app.database'));
		$summonProcessor->setBilling($this->get('app.billing_operations'));
		$summonProcessor->setRequestOps($this->get('app.request_operations'));
		$summonProcessor->setMandrillOps($this->get('app.mandrill_operations'));
		$summonProcessor->runValidation();
		$summonProcessor->performAction();

		$response = new Response();
		$response->setContent($summonProcessor->getSerialized());
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	* @Route("/internal/services/requests/{requestToken_}/{porterToken_}/{action_}", name="internal_services_requests", requirements={"requestToken_" : "([a-zA-Z\d]{1,})", "porterToken_" : "([a-zA-Z\d]{1,})", "action_" : "view|take|give|start|end"}))
	*/
	public function porterRequestAction(Request $request_, string $requestToken_, string $porterToken_, string $action_ = 'view') {
		$claimed 	= false;			//	true when the porter has claimed the request
		$available 	= false;			//	true if the request has open positions available
		$goWindow	= false;			//	true when within 24 hours of the request start time
		$goTime 	= false;			//	true when the request is supposed to have started
		$porterPay  = 0;
		$porterActivitySummary 	= null;	//	summary of the porters activity
		$startActivity			= null;	//	the start activity for the porter - only is not null if being created or already exists
		$endActivity			= null;	//	the end activity for the porter - only is not null if being created or already exists
		//	getting porter from db via token
		$porter 				= $this->get('app.database')->getPorterFromToken($porterToken_);
		//	getting request from db via token
		$request 				= $this->get('app.database')->getRequestFromToken($requestToken_);

		//	only continue if porter and request are not null
		if ($porter !== null && $request !== null) {
			
			//	get activity summary
			$porterActivitySummary = $this->get('app.request_operations')->getPorterActivitySummary($request, $porter, true);
			//	assign activity objects if they are not new
			if ($porterActivitySummary->getStartActivity()->getId() !== null)
				$startActivity = $porterActivitySummary->getStartActivity();
			if ($porterActivitySummary->getEndActivity()->getId() !== null)
				$endActivity = $porterActivitySummary->getEndActivity();

			//	update state variables
			$claimed		= $request->hasPorterAssigned($porter);
			$available 		= $request->getAssignedPorterCount() < $request->getPortersWanted();
			$goWindow		= $this->get('app.request_operations')->isRequestGoWindowOpen($request);
			$goTime			= $this->get('app.request_operations')->isRequestStarted($request);

			//	calculating porter pay
			$porterPay 		= $this->get('app.request_operations')->getPorterPay($request, false);
			
			//==========================
			//	LOGIC
			//==========================
			switch ($action_) {
				//	if the porter is trying to take the request
				case 'take':
					if ($available === true && $claimed === false && $goTime === false) {
						if ($this->get('app.request_operations')->addNewAssignment($request, $porter) === true) {
							//	update entity
							$this->get('app.database')->updateEntities([$request], true, true);
						}
					}
					return $this->redirectToRoute('internal_services_requests', ['requestToken_' => $requestToken_, 'porterToken_' => $porterToken_]);
				//	if the porter is trying to surrender the request
				case 'give':
					if ($goTime == false && $claimed === true) {
						//	update request and remove assignment / activities
						$request = $this->get('app.request_operations')->removeExistingAssignment($request, $porter);
						//	update request
						$this->get('app.database')->updateEntities([$request], true, true);
					}
					return $this->redirectToRoute('internal_services_requests', ['requestToken_' => $requestToken_, 'porterToken_' => $porterToken_]);
				//	the porter has the request and is saying that they have started the job
				case 'start':
					if ($claimed === true && $goWindow === true && $startActivity === null) {
						//	set the start activity
						$startActivity = $porterActivitySummary->getStartActivity();
						//	create new DateTime object
						$time = $this->get('app.time_operations')->createDateTimeForRequest(null, $request);
						//	update the activity
						$startActivity->setActivityDateTime($time);
						//	push it to the DB via Doctrine
						$this->get('app.database')->addEntities([$startActivity], true, true);
					}
					return $this->redirectToRoute('internal_services_requests', ['requestToken_' => $requestToken_, 'porterToken_' => $porterToken_]);
				//	the porter has the request and is saying they've finished the job
				case 'end':
					if ($claimed === true && $goWindow === true && $startActivity !== null && $endActivity === null) {
						//	set the end activity
						$endActivity = $porterActivitySummary->getEndActivity();
						//	create new DateTime object
						$time = $this->get('app.time_operations')->createDateTimeForRequest(null, $request);
						//	update the activity
						$endActivity->setActivityDateTime($time);
						//	push it to the DB via Doctrine
						$this->get('app.database')->addEntities([$endActivity], true, true);
						//	move request from active to worked if all porters have finished
						$this->get('app.request_operations')->transitionRequestToWorkedByPorters($request);
					}
					return $this->redirectToRoute('internal_services_requests', ['requestToken_' => $requestToken_, 'porterToken_' => $porterToken_]);
			}

		}
		
    	return $this->render('external/porterRequestView.html.twig', ['requestToken' => $requestToken_, 'porterToken' => $porterToken_, 'claimed' => $claimed, 'available' => $available, 'goWindow' => $goWindow, 'goTime' => $goTime, 'request' => $request, 'porter' => $porter, 'porterPay' => $porterPay, 'startActivity' => $startActivity, 'endActivity' => $endActivity]);
	}

}