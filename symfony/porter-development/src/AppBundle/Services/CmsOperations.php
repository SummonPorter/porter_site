<?php

// src/AppBundle/Services/CmsOperations.php
namespace AppBundle\Services;

use AppBundle\Utilities\FormHandler\FormHandler;
use AppBundle\Utilities\Misc\RoutePath;
use AppBundle\Services\FormHandlerFactory;
use AppBundle\Services\RequestOperations;
use AppBundle\Services\BillingOperations;
use AppBundle\Services\Database;
use AppBundle\Services\MathOperations;
use AppBundle\Security\SecurityOperations;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\AbstractType;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;

use \DateTime;

class CmsOperations {

    //  services
	protected $router;
	protected $doctrine;
	protected $formFactory;
    protected $requestOps;
    protected $billingOps;
    protected $database;
    protected $mathOps;
    protected $securityOps;
    protected $logger;

    //  other data members
	protected $doctrinePath;
	protected $entityClass;
    protected $sourcePath;
	protected $formHandlers;

	public function __construct(Router $router_, Registry $doctrine_, FormFactory $formFactory_, RequestOperations $requestOps_, BillingOperations $billingOps_, Database $database_, MathOperations $mathOps_, SecurityOperations $securityOps_, Logger $logger_) {
		$this->router         = $router_;
		$this->doctrine       = $doctrine_;
		$this->formFactory    = $formFactory_;
        $this->requestOps     = $requestOps_;
        $this->billingOps     = $billingOps_;
        $this->database       = $database_;
        $this->mathOps        = $mathOps_;
        $this->securityOps    = $securityOps_;
        $this->logger         = $logger_; 
		$this->formHandlers = [];
	}

	public function setup(string $doctrinePath_, string $entityClass_, RoutePath $sourcePath_) {
		$this->doctrinePath = $doctrinePath_;
		$this->entityClass = $entityClass_;
        $this->sourcePath = $sourcePath_;
	}

	public function getRouter(): Router {
		return $this->router;
	}

	public function getDoctrine():Registry {
		return $this->doctrine;
	}

	public function getFormFactory(): FormFactory {
		return $this->formFactory;
	}

    public function getRequestOps(): RequestOperations {
        return $this->requestOps;
    }

    public function getBillingOps(): BillingOperations {
        return $this->billingOps;
    }

    public function getDatabase(): Database {
        return $this->database;
    }

    public function getMathOps(): MathOperations {
        return $this->mathOps;
    }

    public function getSecurityOps(): SecurityOperations {
        return $this->securityOps;
    }

    public function getLogger(): Logger {
        return $this->logger;
    }

	public function getDoctrinePath(): string {
		return $this->doctrinePath;
	}

	public function getEntityClass(): string {
		return $this->entityClass;
	}

    public function getSourcePath(): RoutePath {
        return $this->sourcePath;
    }

    public function getSourceRoute(): string {
        return $this->getRouter()->generate($this->getSourcePath()->getRouteName(), $this->getSourcePath()->getRouteParameters(), UrlGeneratorInterface::ABSOLUTE_PATH);
    }

	public function addFormHandler(FormHandler $formHandler_, string $key_) {
		$this->formHandlers[$key_] = $formHandler_;
	}

	//	convenience method for building a form from a form handler
	public function buildForm(string $key_, Array $entities_ = null) {
		return $this->formHandlers[$key_]->buildForm($this, $entities_);
	}

	//	iterates through all form handlers and returns redirect responses or null
	//	flash messages may also be added to the current session
	public function manageSubmits(Request $request_) {
		foreach($this->formHandlers as $formHandler) {
			$result = $this->processFormRequest($formHandler->getFormClass(), $request_);
			if ($result['valid'])
				return $formHandler->manageSubmit($this, $request_, $result);
		}

        return null;
	}

	//  checks if submitted data is valid, returns array of results
    protected function processFormRequest($class_, Request $request_) {
        //  creating blank form
        $form = $this->formFactory->create($class_, null, []);
        //  trying to handle the request
        $form->handleRequest($request_);

        //  if valid and submitted, call appropriate method
        if ($form->isSubmitted() && $form->isValid())
            return ['form' => $form, 'valid' => true, 'data' => $form->getData()];

        return ['form' => $form, 'valid' => false, 'data' => $form->getData()];
    }

    //  generates page marks for front end display / user navigation for CMS lists
    public function buildPageMarks(int $dbCount_, int $cPage_) {
        $pageMarks = [];

        $tPages = 0;
        if ($dbCount_ > 0)
            $tPages = ceil($dbCount_ / 50);
        if ($cPage_ > $tPages)
            $cPage_ = $tPages;

        //  1, ..., p-4, p-2, p-1, p, p+1, p+2, p+4, ..., t

        //  work backwards from current page
        if ($cPage_ > 5) {
            $pageMarks[] = ['url' => 'page:1', 'text' => '1'];
            if ($cPage_ > 6)
                $pageMarks[] = ['text' => '...'];
        }

        if ($cPage_ > 4)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ - 4), 'text' => strval($cPage_ - 4)];
        elseif ($cPage_ > 3)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ - 3), 'text' => strval($cPage_ - 3)];

        if ($cPage_ > 2)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ - 2), 'text' => strval($cPage_ - 2)];
        if ($cPage_ > 1)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ - 1), 'text' => strval($cPage_ - 1)];

        //  current page
        $pageMarks[] = ['text' => '[' . strval($cPage_) . ']'];

        //  working forwards
        if ($tPages - $cPage_ >= 1)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ + 1), 'text' => strval($cPage_ + 1)];
        if ($tPages - $cPage_ >= 2)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ + 2), 'text' => strval($cPage_ + 2)];

        if ($tPages - $cPage_ >= 4)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ + 4), 'text' => strval($cPage_ + 4)];
        elseif ($tPages - $cPage_ >= 3)
            $pageMarks[] = ['url' => 'page:' . strval($cPage_ + 3), 'text' => strval($cPage_ + 3)];

        if ($tPages - $cPage_ >= 5) {
            if ($tPages - $cPage_ >= 6)
                $pageMarks[] = ['text' => '...'];
            $pageMarks[] = ['url' => 'page:' . strval($tPages), 'text' => strval($tPages)];
        }

        return $pageMarks;
	}

}