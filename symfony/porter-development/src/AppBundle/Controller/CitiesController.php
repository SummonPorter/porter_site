<?php

// src/AppBundle/Controller/CitiesController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\City\CitiesListInsertType;
use AppBundle\Form\City\CitiesListUpdateType;
use AppBundle\Form\ListInsertType;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Entity\CitiesDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CitiesController extends CmsController {

    public function __construct() {
        parent::__construct();
    }

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations() {
        $this->get('app.cms_operations')->setup('AppBundle:CitiesDB', CitiesDB::class, new RoutePath('cities_controller_main'));
        $this->addFormHandler('main_form',          'cities_main_form', CheckedEntitiesDeleteListType::class,   new RoutePath('cities_controller_main'));
        $this->addFormHandler('main_insert_form',   'main_insert_form', ListInsertType::class,                  new RoutePath('cities_controller_insert'));
        $this->addFormHandler('insert_form',        'insert_form',      CitiesListInsertType::class,            new RoutePath('cities_controller_main'));
        $this->addFormHandler('update_form',        'update_form',      CitiesListUpdateType::class,            new RoutePath('cities_controller_main'));
    }

    //========================================================================================
    //  Actions
    //========================================================================================

	/**
	* @Route("/cities", name="cities_controller_main")
	*/
	public function mainAction(Request $request_) {
        $this->setupCmsOperations();
        
        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources();

        return $this->render('city/cities.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => $resources['insertForm']->createView(), 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/cities/insert/{count_}", name="cities_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations();

        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++)
            $entities[] = new CitiesDB();

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

		return $this->render('city/addCities.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/cities/details/{id_}", name="cities_controller_details", requirements={"id_": "\d+"}))
    */
    public function detailsAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        $city = $this->get('app.database')->getCity($id_);
        return $this->render('city/detailsCities.html.twig', array('city' => $city));
    }

    /**
    * @Route("/cities/update/{id_}", name="cities_controller_update", requirements={"id_": "\d+"}))
    */
    public function updateAction(Request $request_, int $id_) {
        $this->setupCmsOperations();
        
        //  creating array to put entity into
        $entities = [$this->get('app.database')->getCity($id_)];

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('update_form', $entities);

        return $this->render('city/updateCities.html.twig', array('listForm' => $listForm->createView()));
    }

}