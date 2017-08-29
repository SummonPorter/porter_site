<?php

// src/AppBundle/Controller/RegionsController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\Region\RegionsListInsertType;
use AppBundle\Form\Region\RegionsListUpdateType;
use AppBundle\Form\ListInsertType;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Entity\RegionsDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegionsController extends CmsController {

    public function __construct() {
        parent::__construct();
    }

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations() {
        $this->get('app.cms_operations')->setup('AppBundle:RegionsDB', RegionsDB::class, new RoutePath('regions_controller_main'));
        $this->addFormHandler('main_form',          'regions_main_form',    CheckedEntitiesDeleteListType::class,   new RoutePath('regions_controller_main'));
        $this->addFormHandler('main_insert_form',   'main_insert_form',     ListInsertType::class,                  new RoutePath('regions_controller_insert'));
        $this->addFormHandler('insert_form',        'insert_form',          RegionsListInsertType::class,           new RoutePath('regions_controller_main'));
        $this->addFormHandler('update_form',        'update_form',          RegionsListUpdateType::class,           new RoutePath('regions_controller_main'));
    }

    //========================================================================================
    //  Actions
    //========================================================================================

	/**
	* @Route("/regions", name="regions_controller_main")
	*/
	public function mainAction(Request $request_) {
        $this->setupCmsOperations();

        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources();

        return $this->render('region/regions.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => $resources['insertForm']->createView(), 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/regions/insert/{count_}", name="regions_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations();

        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++)
            $entities[] = new RegionsDB();

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

		return $this->render('region/addRegions.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/regions/details/{id_}", name="regions_controller_details", requirements={"id_": "\d+"}))
    */
    public function detailsAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        $region = $this->get('app.database')->getRegion($id_);
        return $this->render('region/detailsRegions.html.twig', array('region' => $region));
    }

    /**
    * @Route("/regions/update/{id_}", name="regions_controller_update", requirements={"id_": "\d+"}))
    */
    public function updateAction(Request $request_, int $id_) {
        $this->setupCmsOperations();
        
        //  creating array to put entity into
        $entities = [$this->get('app.database')->getRegion($id_)];

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('update_form', $entities);
        
        return $this->render('region/updateRegions.html.twig', array('listForm' => $listForm->createView()));
    }

}