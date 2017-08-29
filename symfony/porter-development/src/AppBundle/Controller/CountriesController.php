<?php

// src/AppBundle/Controller/CountriesController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\Country\CountriesListInsertType;
use AppBundle\Form\Country\CountriesListUpdateType;
use AppBundle\Form\ListInsertType;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Entity\CountriesDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Swift_Message;

class CountriesController extends CmsController {

    public function __construct() {
        parent::__construct();
    }

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations() {
        $this->get('app.cms_operations')->setup('AppBundle:CountriesDB', CountriesDB::class, new RoutePath('countries_controller_main'));
        $this->addFormHandler('main_form',          'countries_main_form',  CheckedEntitiesDeleteListType::class,   new RoutePath('countries_controller_main'));
        $this->addFormHandler('main_insert_form',   'main_insert_form',     ListInsertType::class,                  new RoutePath('countries_controller_insert'));
        $this->addFormHandler('insert_form',        'insert_form',          CountriesListInsertType::class,         new RoutePath('countries_controller_main'));
        $this->addFormHandler('update_form',        'update_form',          CountriesListUpdateType::class,         new RoutePath('countries_controller_main'));
    }

    //========================================================================================
    //  Actions
    //========================================================================================
    
	/**
	* @Route("/countries", name="countries_controller_main")
	*/
	public function mainAction(Request $request_) {
        $this->setupCmsOperations();

        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources();

        return $this->render('country/countries.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => $resources['insertForm']->createView(), 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/countries/insert/{count_}", name="countries_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations();
        
        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++)
            $entities[] = new CountriesDB();

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

		return $this->render('country/addCountries.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/countries/details/{id_}", name="countries_controller_details", requirements={"id_": "\d+"}))
    */
    public function detailsAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        $country = $this->get('app.database')->getCountry($id_);
        return $this->render('country/detailsCountries.html.twig', array('country' => $country));
    }

    /**
    * @Route("/countries/update/{id_}", name="countries_controller_update", requirements={"id_": "\d+"}))
    */
    public function updateAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        //  creating array to put entity into
        $entities = [$this->get('app.database')->getCountry($id_)];

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('update_form', $entities);
        
        return $this->render('country/updateCountries.html.twig', array('listForm' => $listForm->createView()));
    }
    
}