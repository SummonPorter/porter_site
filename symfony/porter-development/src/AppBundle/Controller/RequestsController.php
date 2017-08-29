<?php

// src/AppBundle/Controller/RequestsController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\ListInsertType;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Form\Request\RequestsProcessType;
use AppBundle\Form\Request\NewRequestsListInsertType;
use AppBundle\Form\Request\NewRequestsListUpdateType;
use AppBundle\Form\Request\ActiveRequestsListUpdateType;
use AppBundle\Form\Request\WorkedRequestsListUpdateType;
use AppBundle\Entity\RequestsDB;
use AppBundle\Entity\CustomersDB;
use AppBundle\Entity\NewRequestEntity;
use AppBundle\Entity\NewUpdateRequestEntity;
use AppBundle\Entity\ActiveUpdateRequestEntity;
use AppBundle\Entity\ActivePorterAssignmentEntity;
use AppBundle\Entity\WorkedUpdateRequestEntity;
use AppBundle\Entity\PorterRequestActivitySummary;
use AppBundle\Entity\RequestProcess;
use AppBundle\Entity\CheckedEntity;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RequestsController extends CmsController {

    public function __construct() {
        parent::__construct();
    }

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations(string $filter_) {
        $this->get('app.cms_operations')->setup('AppBundle:RequestsDB', RequestsDB::class, new RoutePath('requests_controller_main', ['filter_' => $filter_]));

        switch ($filter_) {
            case 'new':
                $this->addFormHandler('main_form',          'main_form',                    CheckedEntitiesDeleteListType::class,   new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                //$this->addFormHandler('main_insert_form',   'main_insert_form',             ListInsertType::class,                  new RoutePath('requests_controller_insert'));
                //$this->addFormHandler('insert_form',        'requests_insert_form',         NewRequestsListInsertType::class,       new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                $this->addFormHandler('process_form',       'requests_process_form',        RequestsProcessType::class,             new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                $this->addFormHandler('update_form',        'requests_new_update_form',     NewRequestsListUpdateType::class,       new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                break;
            case 'active':
                $this->addFormHandler('main_form',          'main_form',                    CheckedEntitiesDeleteListType::class,   new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                $this->addFormHandler('process_form',       'requests_process_form',        RequestsProcessType::class,             new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                $this->addFormHandler('update_form',        'requests_active_update_form',  ActiveRequestsListUpdateType::class,    new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                break;
            case 'worked':
                $this->addFormHandler('main_form',          'main_form',                    CheckedEntitiesDeleteListType::class,   new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                $this->addFormHandler('process_form',       'requests_process_form',        RequestsProcessType::class,             new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                $this->addFormHandler('update_form',        'requests_worked_update_form',  WorkedRequestsListUpdateType::class,    new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                break;
            case 'complete':
                $this->addFormHandler('main_form',          'main_form',                    CheckedEntitiesDeleteListType::class,   new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                //$this->addFormHandler('process_form',       'requests_process_form',        RequestsProcessType::class,             new RoutePath('requests_controller_main', ['filter_' => $filter_]));
                break;
        }
    }

    //========================================================================================
    //  Actions
    //========================================================================================

    /**
    * @Route("/", name="requests_controller_default")
    */
    public function defaultAction(Request $request_) {
        //return $this->mainAction($request_, 'new');
        return $this->redirectToRoute('requests_controller_main', ['filter_' => 'new']);
    }

	/**
	* @Route("/requests/{filter_}", name="requests_controller_main", requirements={"filter_" : "new|active|worked|complete"})
	*/
	public function mainAction(Request $request_, string $filter_) {
        $this->setupCmsOperations($filter_);
        
        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources($filter_);

        if ($filter_ === 'new')
            return $this->render('request/newRequests.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => null, 'pageMarks' => $resources['pageMarks']));
        if ($filter_ === 'active')
            return $this->render('request/activeRequests.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => null, 'pageMarks' => $resources['pageMarks']));
        if ($filter_ === 'worked')
            return $this->render('request/workedRequests.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => null, 'pageMarks' => $resources['pageMarks']));
        if ($filter_ === 'complete')
            return $this->render('request/completeRequests.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => null, 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/requests/insert/{count_}", name="requests_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations('new');
        
        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++) {
            $request = new RequestsDB();
            $customer = new CustomersDB();
            $request->setCustomer($customer);
            $entities[] = new NewRequestEntity($request, $customer);
        }

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

        return $this->render('request/addRequests.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/requests/details/{filter_}/{id_}", name="requests_controller_details", requirements={"filter_" : "new|active|worked|complete", "id_": "\d+"}))
    */
    public function detailsAction(Request $request_, string $filter_, int $id_) {
        $this->setupCmsOperations($filter_);

        //  fetching the request
        $request = $this->get('app.database')->getRequest($id_);

        //  TODO: compare filter_ to request state - if they are different redirect to requests_controller_main
        
        switch ($filter_) {
            case 'new':
                //  building the RequestProcess from the fetched Request object
                $processEntity = new RequestProcess($request, 'active', 'Approve');
                //  building insert list form
                $processForm = $this->getCmsOps()->buildForm('process_form', [$processEntity]);
                return $this->render('request/detailsNewRequests.html.twig', array('processForm' => $processForm->createView(), 'request' => $request));
            case 'active':
                //  building the RequestProcess from the fetched Request object
                $processEntity = new RequestProcess($request, 'worked', 'Override To Worked');
                //  building insert list form
                $processForm = $this->getCmsOps()->buildForm('process_form', [$processEntity]);
                return $this->render('request/detailsActiveRequests.html.twig', array('processForm' => $processForm->createView(), 'request' => $request));
            case 'worked':
                //  building the RequestProcess from the fetched Request object
                $processEntity = new RequestProcess($request, 'complete', 'Bill Customer');
                //  getting porter summary list
                $porterSummaries = $this->get('app.request_operations')->getPorterActivitySummaries($request);
                //  TODO: this will need to be fleshed out better to support individual porters
                //  getting the porter pay from the request for the porters and passing it along to the template
                $porterPay = $this->get('app.request_operations')->getPorterPay($request);
                //  building insert list form
                $processForm = $this->getCmsOps()->buildForm('process_form', [$processEntity]);
                return $this->render('request/detailsWorkedRequests.html.twig', array('processForm' => $processForm->createView(), 'request' => $request, 'summaries' => $porterSummaries, 'porterPay' => $porterPay));
            case 'complete':
                //  getting porter summary list
                $porterSummaries = $this->get('app.request_operations')->getPorterActivitySummaries($request);
                //  TODO: this will need to be fleshed out better to support individual porters
                //  getting the porter pay from the request for the porters and passing it along to the template
                $porterPay = $this->get('app.request_operations')->getPorterPay($request);
                return $this->render('request/detailsCompleteRequests.html.twig', array('request' => $request, 'summaries' => $porterSummaries, 'porterPay' => $porterPay));
        }
    }

    /**
    * @Route("/requests/update/{filter_}/{id_}", name="requests_controller_update", requirements={"filter_" : "new|active|worked|complete", "id_": "\d+"}))
    */
    public function updateAction(Request $request_, string $filter_, int $id_) {
        $this->setupCmsOperations($filter_);

        //  fetching the request
        $request = $this->get('app.database')->getRequest($id_);

        //  TODO: compare filter_ to request state - if they are different redirect to requests_controller_main
        
        if ($filter_ === 'new') {
            //  building the NewUpdateRequestEntity from the fetched Request object
            $updateEntity = new NewUpdateRequestEntity($request);
            //  building insert list form
            $listForm = $this->getCmsOps()->buildForm('update_form', [$updateEntity]);

            return $this->render('request/updateNewRequests.html.twig', array('listForm' => $listForm->createView(), 'customer' => $request->getCustomer()));
        }
        elseif ($filter_ === 'active') {
            //  building the ActiveUpdateRequestEntity from the fetched Request object
            $updateEntity = new ActiveUpdateRequestEntity($request);
            //  adding blank porter assignments
            while ($updateEntity->getRPorterAssignmentsCount() < $updateEntity->getRPortersWanted())
                $updateEntity->addRPorterAssignments(null);
            //  building insert list form
            $listForm = $this->getCmsOps()->buildForm('update_form', [$updateEntity]);

            return $this->render('request/updateActiveRequests.html.twig', array('listForm' => $listForm->createView(), 'customer' => $request->getCustomer(), 'requestEntity' => $request));
        }

        elseif ($filter_ === 'worked') {
            //  building the ActiveUpdateRequestEntity from the fetched Request object
            $updateEntity = new WorkedUpdateRequestEntity($request);
            //  add PorterRequestActivitySummary objects
            $updateEntity->setPorterSummaries($this->get('app.request_operations')->getPorterActivitySummaries($request, true));
            //  adding blank activity summaries if needed
            while ($updateEntity->getPorterSummariesCount() < $request->getPortersWanted())
                $updateEntity->addPorterSummary(new PorterRequestActivitySummary($request, null, null, null));
            //  building insert list form
            $listForm = $this->getCmsOps()->buildForm('update_form', [$updateEntity]);

            return $this->render('request/updateWorkedRequests.html.twig', array('listForm' => $listForm->createView(), 'customer' => $request->getCustomer(), 'requestEntity' => $request));
        }
    }

    //========================================================================================
    //  Overridden
    //========================================================================================

    //  builds resources to be rendered in template pages
    //  overridden from CmsController
    protected function buildPageResources($option_ = null): Array {
        $resources = [];

        //  get entity data from DB
        $dbEntities = $this->get('app.database')->getRequests($option_);
        //  get count of entities in DB
        $dbEntitiesCount = count($dbEntities);

        //  building main list data
        $checkedEntities = [];
        foreach ($dbEntities as $entity)
            $checkedEntities[] = new CheckedEntity($this->getCmsOps()->getDoctrinePath(), $this->getCmsOps()->getEntityClass(), $entity);

        //  building insert list form
        /*if ($option_ === 'new')
            $resources['insertForm']    = $this->getCmsOps()->buildForm('main_insert_form', null);*/
        //  building main form
        $resources['listForm']          = $this->getCmsOps()->buildForm('main_form', $checkedEntities);
        //  building page marks
        $resources['pageMarks']         = $this->getCmsOps()->buildPageMarks($dbEntitiesCount, $this->getCurrentPage(1, $dbEntitiesCount));

        return $resources;
    }

}