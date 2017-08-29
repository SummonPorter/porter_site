<?php

// src/AppBundle/Controller/ServicesController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Form\ListInsertType;
use AppBundle\Form\Service\ServicesListInsertType;
use AppBundle\Form\Service\ServicesListUpdateType;
use AppBundle\Entity\ServicesDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ServicesController extends CmsController {

    public function __construct() {
        parent::__construct();
    }

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations() {
        $this->get('app.cms_operations')->setup('AppBundle:ServicesDB', ServicesDB::class, new RoutePath('services_controller_main'));
        $this->addFormHandler('main_form',          'services_main_form',   CheckedEntitiesDeleteListType::class,   new RoutePath('services_controller_main'));
        $this->addFormHandler('main_insert_form',   'main_insert_form',     ListInsertType::class,                  new RoutePath('services_controller_insert'));
        $this->addFormHandler('insert_form',        'services_insert_form', ServicesListInsertType::class,          new RoutePath('services_controller_main'));
        $this->addFormHandler('update_form',        'update_form',          ServicesListUpdateType::class,          new RoutePath('services_controller_main'));
    }

    //========================================================================================
    //  Actions
    //========================================================================================

	/**
	* @Route("/services", name="services_controller_main")
	*/
	public function defaultAction(Request $request_) {
        $this->setupCmsOperations();

        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources();

        return $this->render('service/services.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => $resources['insertForm']->createView(), 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/services/insert/{count_}", name="services_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations();

        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++)
            $entities[] = new ServicesDB();

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

		return $this->render('service/addServices.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/services/details/{id_}", name="services_controller_details", requirements={"id_": "\d+"}))
    */
    public function detailsAction(Request $request_, int $id_) {
        $service = $this->get('app.database')->getService($id_);
        return $this->render('service/detailsServices.html.twig', array('service' => $service));
    }

    /**
    * @Route("/services/update/{id_}", name="services_controller_update", requirements={"id_": "\d+"}))
    */
    public function updateAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        //  creating array to put entity into
        $entities = [$this->get('app.database')->getService($id_)];

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('update_form', $entities);
        
        return $this->render('service/updateServices.html.twig', array('listForm' => $listForm->createView()));
    }

}