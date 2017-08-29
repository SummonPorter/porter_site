<?php

// src/AppBundle/Services/RequestOperations.php
namespace AppBundle\Services;

use AppBundle\Entity\CitiesDB;
use AppBundle\Entity\CountriesDB;
use AppBundle\Entity\RegionsDB;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Entity\DiscountsDB;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\CustomersDB;
use AppBundle\Entity\CreditCard;
use AppBundle\Entity\PorterAssignmentsDB;
use AppBundle\Entity\PorterRequestActivityDB;
use AppBundle\Entity\PorterRequestActivitySummary;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Entity\MandrillEmailOp;
use AppBundle\Services\MathOperations;
use AppBundle\Services\TimeOperations;
use AppBundle\Services\MandrillOperations;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Monolog\Logger;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RequestOperations {

	private $database;
	private $mathOps;
	private $timeOps;
	private $doctrine;
    private $mandrillOps;
	private $router;
	private $logger;
	private $em;

    public function __construct(Database $database_, MathOperations $mathOps_, TimeOperations $timeOps_, MandrillOperations $mandrillOps_, Registry $doctrine_, Router $router_, Logger $logger_) {
    	$this->database = $database_;
    	$this->mathOps = $mathOps_;
    	$this->timeOps = $timeOps_;
        $this->mandrillOps = $mandrillOps_;
        $this->doctrine = $doctrine_;
        $this->router = $router_;
        $this->logger = $logger_;
        $this->em = $this->doctrine->getManager();
    }

    //========================================================================================
	//	Public Methods
	//========================================================================================

    //	updates the billing and time information for request_
	public function updateRequestNumbers(RequestsDB $request_): RequestsDB {
		$this->updatePorterDuration($request_);
		$this->updateTotalBilling($request_);
		$request_ = $this->em->merge($request_);
		$this->em->flush();
		return $request_;
	}

	public function getPossiblePortersForRequest(RequestsDB $request_, $includeAssigned_ = false, $includeBusy_ = false) {
		
		$assignedPorters = [];
		//	get already assigned porters, if they should be excluded from search
		if ($includeAssigned_ === false)
			$assignedPorters = $this->getAssignedPorterIds($request_);
		//	query database for porters not already assigned
		$availablePorters = $this->getUnassignedPorters($request_, $assignedPorters);
		//	filter available porters by location and service interest
		$filteredPorters = $this->filterAvailablePorters($request_, $availablePorters);
		//	TODO: filter porters by availability
		if ($includeBusy_ === false) {
			
		}

		return $filteredPorters;
	}

	public function getPorterActivitySummary(RequestsDB $request_, PortersDB $porter_, $allowPartials_ = false) {
		//	get activities
        $results = $this->database->getPorterActivities($request_, $porter_);
        
        $activityCount = count($results);
		//	there should be 2
        if ($activityCount !== 2 && $allowPartials_ === false)
        	return null;

        $activityA = $activityB = null;
        if ($activityCount >= 2) {
        	$activityA = $results[0];
        	$activityB = $results[1];
        }
        elseif ($activityCount === 1)
        	$activityA = $results[0];

        return new PorterRequestActivitySummary($request_, $porter_, $activityA, $activityB);
	}

	public function getPorterActivitySummaries(RequestsDB $request_, $allowPartials_ = false): array {
    	$porterSummaries = [];
    	$assignedPorters = $request_->getPorterAssignments();

    	//	for each assigned porter, retrieve their activity related to the request
    	foreach ($assignedPorters as $assignedPorter) {
	        $porterSummary = $this->getPorterActivitySummary($request_, $assignedPorter->getPorter(), $allowPartials_);
	        if ($porterSummary !== null)
				$porterSummaries[] = $porterSummary;
    	}

    	return $porterSummaries;
    }

    //	returns the amount of billing time for a specific request, based on the service
    public function getBillingTime(RequestsDB $request_, bool $actual_ = true) {
    	if ($request_->getService() === null)
			return 0;

		//	defaults to using estimate duration of request
		$minutes = $request_->getEstDuration();
		if ($actual_ === true)
    		$minutes = $request_->getPorterDurationInMin();
		$billingTime = 0;

		//	determine billing amount, if service is per hour or per minute
		if ($request_->getService()->isPerHour())
			$billingTime = ceil($minutes / 15) / 4;
		elseif ($request_->getService()->isPerMinute())
			$billingTime = $minutes;

		return $billingTime;
    }

    //	returns the amount porters should get payed. if actual_ is false, it works of estimated duration of request
    public function getPorterPay(RequestsDB $request_, bool $actual_ = true) {
    	if ($request_->getService() === null)
			return 0;

		$time = $this->getBillingTime($request_, $actual_);
		$amount = $time * $request_->getService()->getPay();
		$amount = round($amount, 2);

		//	safety
		if ($amount < 0)
			$amount = 0;

		return $amount;
    }

    //	returns true if request is within 24 hours of starting
    public function isRequestGoWindowOpen(RequestsDB $request_): bool {
    	//	get current time, using request timezone
    	$nowTime = $this->timeOps->createDateTimeForRequest(null, $request_);
    	$startTime = $this->timeOps->createStartDateTimeForRequest($request_);
    	$difference = $startTime->format('U') - $nowTime->format('U');
    	//	converting to minutes
    	$difference /= 60;
    	//	if difference is less that 24 * 60, then the go window is open
    	return $difference <= (24 * 60);
    }

    //	returns true if request is supposed to have started already
    public function isRequestStarted(RequestsDB $request_): bool {
    	//	get current time, using request timezone
    	$nowTime = $this->timeOps->createDateTimeForRequest(null, $request_);
    	$startTime = $this->timeOps->createStartDateTimeForRequest($request_);
    	$difference = $startTime->format('U') - $nowTime->format('U');
    	//	request has started if there is no time left
    	return $difference <= 0;
    }

    //	updates the request_ object, returns true if the new assignment was successful or not
    //	WARNING - request still needs to be updated in DB via Doctrine
    public function addNewAssignment(RequestsDB &$request_, PortersDB $porter_): bool {
    	if ($request_->getPortersWanted() > $request_->getAssignedPorterCount()) {
    		$newAssignment = new PorterAssignmentsDB();
			$newAssignment->setPorter($porter_);
            $newAssignment->setAssignmentEpoch(round(microtime(true) * 1000));
            $request_->addPorterAssignment($newAssignment);
            //	email porter that they have a new job
            $this->alertPorterAboutAssignment($request_, $porter_);
            return true;
        }

        return false;
    }

    //	updates the request_ object and returns it without the porter assigned
    //	WARNING - request still needs to be updated in DB via Doctrine, the assignment and activities are removed from DB
    public function removeExistingAssignment(RequestsDB $request_, PortersDB $porter_): RequestsDB {
    	//  search for any PorterRequestActivityDB objects associated with the porter
        $activities = $this->database->getPorterActivities($request_, $porter_);
        //  remove those activities from the database
        $this->database->deleteEntities($activities);

        //	loop through assignments in request, and then rebuild the array collection
        $assignments = $request_->getPorterAssignments();
        $newAssignmentsCollection = new ArrayCollection();
        foreach ($assignments as $assignment) {
        	//	removing the assignment
        	if ($assignment->getPorter()->getId() === $porter_->getId()) {
        		$this->database->deleteEntities([$assignment]);
        		//	email porter that they have been removed from a job
            	$this->alertPorterAboutUnassignment($request_, $assignment->getPorter());
        	}
        	else
        		$newAssignmentsCollection->add($assignment);
        }

        //	set new collection for request
        $request_->setPorterAssignments($newAssignmentsCollection);

        return $request_;
    }

    public function transitionRequestToWorkedByPorters(RequestsDB $request_) {
    	$completedPorters = $this->getPorterActivitySummaries($request_, false);
    	//	request is done, if count of array matches the porters assigned
    	if (count($completedPorters) === $request_->getAssignedPorterCount()) {
    		$request_->setRequestState('worked');
    		$request_->setIdToken(null);
    		$request_ = $this->updateRequestNumbers($request_);
    		$this->database->updateEntities([$request_], true, true);
    	}
    }

    public function alertPortersAboutNewRequest(RequestsDB $request_) {
    	$subject	= 'New Job (' . $request_->getService()->getName() . ') Available!';

    	$message	= 'A new job is now available in ' . $request_->getRepCity()->getName() . ". Check out the details by following the link below. Please do not reply to this email.<br>";

    	$headers	= "From: The Porter Alert Bot <porter-alerts@summonporter.ca>\r\n";
		$headers	.= "X-Mailer: PHP/" . phpversion(). "\r\n";
		$headers	.= "MIME-Version: 1.0" . "\r\n";
		$headers	.= "Content-type: text/html; charset=iso-8859-1";

		$porters = $this->getPossiblePortersForRequest($request_);

		foreach ($porters as $porter) {
			$topMessage		= 'Hey ' . $porter->getFirstName() . ",<br><br>";
			$bottomMessage	= '<a href="http://www.summonporter.ca' . $this->router->generate('internal_services_requests', ['requestToken_' => $request_->getIdToken(), 'porterToken_' => $porter->getIdToken()], UrlGeneratorInterface::ABSOLUTE_PATH) . '">Check out this job.</a>';
			$bottomMessage  .= "<br><br>The Porter Alert Bot";

			mail($porter->getEmail(), $subject, ($topMessage . $message . $bottomMessage), $headers);
		}
    }

    public function alertPorterAboutAssignment(RequestsDB $request_, PortersDB $porter_) {
    	$subject	= 'You\'ve gotten a job (' . $request_->getService()->getName() . ')!';

    	$message	= 'You have been assigned to a job in ' . $request_->getRepCity()->getName() . ". Check out the details by following the link below. <b>Please keep this email!</b> Please do not reply to this email.<br>";

    	$headers	= "From: The Porter Alert Bot <porter-alerts@summonporter.ca>\r\n";
		$headers   .= "X-Mailer: PHP/" . phpversion(). "\r\n";
		$headers   .= "MIME-Version: 1.0" . "\r\n";
		$headers   .= "Content-type: text/html; charset=iso-8859-1";

		$topMessage		= 'Hey ' . $porter_->getFirstName() . ",<br><br>";
		$bottomMessage	= '<a href="http://www.summonporter.ca' . $this->router->generate('internal_services_requests', ['requestToken_' => $request_->getIdToken(), 'porterToken_' => $porter_->getIdToken()], UrlGeneratorInterface::ABSOLUTE_PATH) . '">Job page.</a>';
		$bottomMessage .= "<br><br>The Porter Alert Bot";

		mail($porter_->getEmail(), $subject, ($topMessage . $message . $bottomMessage), $headers);
    }

	public function alertPorterAboutUnassignment(RequestsDB $request_, PortersDB $porter_) {
    	$msg0  = 'Hey ' . $porter_->getFirstName() . ',<br><br>';
        $msg1  = 'You have been unassigned from a job in ' . $request_->getRepCity()->getName() . '. Please do not reply to this email.<br><br>';
		$msg2  = '<a href="http://www.summonporter.ca' . $this->router->generate('internal_services_requests', ['requestToken_' => $request_->getIdToken(), 'porterToken_' => $porter_->getIdToken()], UrlGeneratorInterface::ABSOLUTE_PATH) . '">Job page.</a>';
		$msg3 .= '<br><br>The Porter Alert Bot';
        $hMsg  = $msg0 . $msg1 . $msg2 . $msg3;

        $msg0  = 'Hey ' . $porter_->getFirstName() . ',\r\n';
        $msg1  = 'You have been unassigned from a job in ' . $request_->getRepCity()->getName() . '. Please do not reply to this email.\r\n';
        $msg2  = 'http://www.summonporter.ca' . $this->router->generate('internal_services_requests', ['requestToken_' => $request_->getIdToken(), 'porterToken_' => $porter_->getIdToken()], UrlGeneratorInterface::ABSOLUTE_PATH) . '\r\n';
        $msg3 .= '\r\nThe Porter Alert Bot';
        $tMsg  = $msg0 . $msg1 . $msg2 . $msg3;

        $from       = "info@summonporter.ca";
        $fName      = "Summon Porter Info";
        $subject    = 'You\'ve been removed from a job (' . $request_->getService()->getName() . ') Available!';

        $mandrillEmail = new MandrillEmailOp($from, $fName, $subject, $hMsg, $tMsg);
        $mandrillEmail->addRecipient($porter_->getEmail(), 'bcc', $porter_->getFullName());

        $this->mandrillOps->sendEmail($mandrillEmail);
    }

    //	builds a new request - returns the new request object if everything went well, or returns null if there was a problem
    public function buildNewRequest(CustomersDB $newCustomer_, ServicesDB $service_, $portersWanted_, $startDateTime_, $estDuration_, $details_, array $locations = [], DiscountsDB $discount_ = null) {
        //  building Request
		$request = new RequestsDB();
        $request->setCustomer($newCustomer_);
        $request->setIdToken(null);
        $request->setService($service_);
        $request->setDiscount($discount_);
        $request->setPortersWanted($portersWanted_);
        $request->setRequestStartDateTime($startDateTime_);
        $request->setEstDuration($estDuration_);
        $request->setDetails($details_);
        
        //	adding addresses to request
        foreach ($locations as $location)
        	$request->addAddress($location);

        //  standard Request setup
        $request->setRequestState('new');
        $request->setRequestEpoch(time());

        return $request;
    }

	//========================================================================================
	//	Time & Porter Methods
	//========================================================================================

    private function updatePorterDuration(RequestsDB $request_) {
    	$porterSummaries = $this->getPorterActivitySummaries($request_);
    	//	safety
    	if (empty($porterSummaries) === true)
    		return $request_;

    	$latestStart = $earliestEnd = null;

    	for ($i = 0; $i < count($porterSummaries); $i++) {
    		$porterSummary = $porterSummaries[$i];

    		//	safety
    		if ($porterSummary->getStartActivity()->getActivityDateTime() === null || $porterSummary->getEndActivity()->getActivityDateTime() === null)
    			return $request_;

    		if ($i === 0) {
    			$latestStart = $porterSummary->getStartActivity()->getActivityDateTime();
    			$earliestEnd = $porterSummary->getEndActivity()->getActivityDateTime();
    		}
			if ($porterSummary->getStartActivity()->getActivityDateTime()->format('U') > $latestStart->format('U'))
				$latestStart = $porterSummary->getStartActivity()->getActivityDateTime();
			if ($porterSummary->getEndActivity()->getActivityDateTime()->format('U') < $earliestEnd->format('U'))
				$earliestEnd = $porterSummary->getEndActivity()->getActivityDateTime();
    	}

    	//	compare times, to start time request for job and correct if needed
    	if ($latestStart->format('U') < $request_->getRequestStartDateTime()->format('U'))
    		$latestStart = $request_->getRequestStartDateTime();
    	if ($earliestEnd->format('U') < $request_->getRequestStartDateTime()->format('U'))
    		$earliestEnd = $request_->getRequestStartDateTime();

    	$request_->setPorterStartDateTime($latestStart);
    	$request_->setPorterEndDateTime($earliestEnd);
    }

    //========================================================================================
	//	Money Methods
	//========================================================================================

    /**
	* WARNING: porter durations must be calculated before calling this method to ensure accurate total
	*/
    private function updateTotalBilling(RequestsDB $request_) {
        $serviceTotal   = $this->getSubtotal($request_);
    	$rollingTotal   = $serviceTotal;
        $trustFee       = $this->getTrustFee($request_, $rollingTotal);

        //  updating the sub total with the trust fee
        $rollingTotal  += $trustFee;
        if ($rollingTotal < 0)
            $rollingTotal = 0;

        //  getting the taxes
    	$taxes = $this->getTaxes($request_, $rollingTotal);
        //  getting the discount
        $discount = $this->getDiscount($request_, $rollingTotal);

        //  setting the total
        $total = ($rollingTotal - $discount) + $taxes;
        if ($total < 0)
            $total = 0;

		//	round number for pennies
		$total = round($total, 2);

        $request_->setBillingService($serviceTotal);
		$request_->setBillingSubtotal($rollingTotal);
		$request_->setBillingDiscount($discount);
        $request_->setBillingTrustFee($trustFee);
		$request_->setBillingTaxes($taxes);
		$request_->setBillingTotal($total);
	}

    private function getSubtotal(RequestsDB $request_) {
		if ($request_->getService() === null)
			return 0;
		
		$billingTime = $this->getBillingTime($request_);
		$subtotal = ($billingTime * $request_->getService()->getFee()) + $request_->getService()->getBaseFee();

		//	multiply subtotal by # of porters working the job
		$subtotal *= $request_->getAssignedPorterCount();

		//	safety
		if ($subtotal < 0)
			$subtotal = 0;

		return round($subtotal, 2);
	}

	private function getDiscount(RequestsDB $request_, $subtotal_) {
		$discount = 0;

		//	calculating discount, if there is one
		if ($request_->getDiscount() !== null) {
			if ($request_->getDiscount()->isPercentage())
				$discount = $subtotal_ * ($request_->getDiscount()->getDiscount() / 100);
			elseif ($request_->getDiscount()->isFlat() && $request_->getDiscount()->getDiscount() < $subtotal_)
				$discount = $request_->getDiscount()->getDiscount();

			//	safety
			if ($discount < 0)
				$discount = 0;
		}

		return round($discount, 2);
	}

    private function getTrustFee(RequestsDB $request_, $subtotal_) {
        $fees = 0;

        $service = $request_->getService();
        if ($service !== null) {
            if ($service->getTrustFee() !== null) {
                $fee = $service->getTrustFee();
                $fees = ($fee - 1.0) * $subtotal_;

                //  safety
                if ($fees < 0)
                    $fees = 0;
            }
        }

        return round($fees, 2);
    }

	private function getTaxes(RequestsDB $request_, $subtotal_) {
		$taxes = 0;

		$city = $request_->getRepCity();
		if ($city !== null) {
			if ($city->getRegion() !== null) {
				$taxRate = $city->getRegion()->getRequestTax();
				$taxes = ($taxRate - 1.0) * $subtotal_;

				//	safety
				if ($taxes < 0)
					$taxes = 0;
			}
		}

		return round($taxes, 2);
	}

	//========================================================================================
	//	Porter Assignment Methods
	//========================================================================================

	//	returns the ids of all porters assigned to the request
	private function getAssignedPorterIds(RequestsDB $request_) {
		$assignedPorters = [];
		$assignments = $request_->getPorterAssignments();
		foreach ($assignments as &$assignment) {
			if ($assignment !== null) {
				if ($assignment->getPorter() !== null)
					$assignedPorters[] = $assignment->getPorter()->getId();
			}
		}

		return $assignedPorters;
	}

	//	queries for all available porters that are not already assigned
	private function getUnassignedPorters(RequestsDB $request_, Array $porterIds_) {
		//	query database for porters that aren't already assigned
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle:PortersDB', 'p')
            ->where('p.active = true');
        if (empty($porterIds_) === false)
			$qb->andWhere($qb->expr()->notIn('p.id', implode(',', $porterIds_)));
		$qb->innerJoin('AppBundle:CitiesDB', 'c', 'WHERE', 'c.region = ' . $request_->getRepCity()->getRegion()->getId());
		
        return $qb->getQuery()->getResult();
	}

	//	ensure porters are in a close enough city and have an interest in the request and are not already busy
	private function filterAvailablePorters(RequestsDB $request_, Array $porters_): Array {
		//	creating list of filtered porters
        $filteredPorters = [];

		foreach ($porters_ as $e) {
			//	determining location
			$locationGood = $this->porterCityIsClose($request_->getRepCity(), $e->getCity());
        	//	checking interest in service
        	$interestGrade = $e->getInterestGradeForService($request_->getService());
        	$interestGood = false;
        	if ($interestGrade !== null)
        		$interestGood = $interestGrade->getInterestGrade() > 0;

        	//	if good location and interest, add to filtered list
        	if ($locationGood && $interestGood)
            	$filteredPorters[] = $e;
        }

        return $filteredPorters;
	}

	//	returns true if the porter city is close enough to the request city
	private function porterCityIsClose(CitiesDB $requestCity_, CitiesDB $porterCity_): bool {
		$dist = $this->mathOps->distanceBetweenCoordinates($requestCity_->getLatitude(), $requestCity_->getLongitude(), $porterCity_->getLatitude(), $porterCity_->getLongitude());
		return $dist < 160000;
	}

}