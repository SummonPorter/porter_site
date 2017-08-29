<?php

// src/AppBundle/Controller/ServiceGroupsController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Form\ListInsertType;
use AppBundle\Form\ServiceGroup\ServiceGroupsListInsertType;
use AppBundle\Form\ServiceGroup\ServiceGroupsListUpdateType;
use AppBundle\Entity\ServiceGroupsDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ServiceGroupsController extends CmsController {

    public function __construct() {
        parent::__construct();
    }

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations() {
        $this->get('app.cms_operations')->setup('AppBundle:ServiceGroupsDB', ServiceGroupsDB::class, new RoutePath('serviceGroups_controller_main'));
        $this->addFormHandler('main_form',          'service_groups_main_form', CheckedEntitiesDeleteListType::class,   new RoutePath('serviceGroups_controller_main'));
        $this->addFormHandler('main_insert_form',   'main_insert_form',         ListInsertType::class,                  new RoutePath('serviceGroups_controller_insert'));
        $this->addFormHandler('insert_form',        'insert_form',              ServiceGroupsListInsertType::class,     new RoutePath('serviceGroups_controller_main'));
        $this->addFormHandler('update_form',        'update_form',              ServiceGroupsListUpdateType::class,     new RoutePath('serviceGroups_controller_main'));
    }

    //========================================================================================
    //  Actions
    //========================================================================================

	/**
	* @Route("/serviceGroups", name="serviceGroups_controller_main")
	*/
	public function mainAction(Request $request_) {
        $this->setupCmsOperations();

        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources();

        return $this->render('serviceGroup/serviceGroups.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => $resources['insertForm']->createView(), 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/serviceGroups/insert/{count_}", name="serviceGroups_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations();

        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++)
            $entities[] = new ServiceGroupsDB();

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

		return $this->render('serviceGroup/addServiceGroups.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/serviceGroups/details/{id_}", name="serviceGroups_controller_details", requirements={"id_": "\d+"}))
    */
    public function detailsAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        $serviceGroup = $this->get('app.database')->getServiceGroup($id_);
        return $this->render('serviceGroup/detailsServiceGroups.html.twig', array('serviceGroup' => $serviceGroup));
    }

    /**
    * @Route("/serviceGroups/update/{id_}", name="serviceGroups_controller_update", requirements={"id_": "\d+"}))
    */
    public function updateAction(Request $request_, int $id_) {
        $this->setupCmsOperations();
        
        //  creating array to put entity into
        $entities = [$this->get('app.database')->getServiceGroup($id_)];

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('update_form', $entities);

        return $this->render('serviceGroup/updateServiceGroups.html.twig', array('listForm' => $listForm->createView()));
    }

}