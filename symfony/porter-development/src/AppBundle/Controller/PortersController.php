<?php

// src/AppBundle/Controller/PortersController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\Porter\PortersListInsertType;
use AppBundle\Form\Porter\PortersListUpdateType;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Form\ListInsertType;
use AppBundle\Entity\PortersDB;
use AppBundle\Entity\PorterVehicleAccessDB;
use AppBundle\Entity\ServiceInterestGradeDB;
use AppBundle\Entity\ServicesDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PortersController extends CmsController {

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations() {
        $this->get('app.cms_operations')->setup('AppBundle:PortersDB', PortersDB::class, new RoutePath('porters_controller_main'));
        $this->addFormHandler('main_form',          'main_form',            CheckedEntitiesDeleteListType::class,   new RoutePath('porters_controller_main'));
        $this->addFormHandler('main_insert_form',   'main_insert_form',     ListInsertType::class,                  new RoutePath('porters_controller_insert'));
        $this->addFormHandler('insert_form',        'porters_insert_form',  PortersListInsertType::class,           new RoutePath('porters_controller_main'));
        $this->addFormHandler('update_form',        'porters_update_form',  PortersListUpdateType::class,           new RoutePath('porters_controller_main'));
    }

    //========================================================================================
    //  Actions
    //========================================================================================

	/**
	* @Route("/porters", name="porters_controller_main")
	*/
	public function mainAction(Request $request_) {
        $this->setupCmsOperations();
        
        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources();

        return $this->render('porter/porters.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => $resources['insertForm']->createView(), 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/porters/insert/{count_}", name="porters_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations();
        
        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++)
            $entities[] = $this->getBlankPorterForInsert();

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

		return $this->render('porter/addPorters.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/porters/details/{id_}", name="porters_controller_details", requirements={"id_": "\d+"}))
    */
    public function detailsAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        $porter = $this->get('app.database')->getPorter($id_);
        return $this->render('porter/detailsPorters.html.twig', array('porter' => $porter));
    }

    /**
    * @Route("/porters/update/{id_}", name="porters_controller_update", requirements={"id_": "\d+"}))
    */
    public function updateAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        //  creating array to put entity into
        $entities = [$this->get('app.database')->getPorter($id_)];

        foreach ($entities as $porter) {

            //  adding blank vehicle access options
            while ($porter->getVehicleAccessCount() < 3)
                $porter->addVehicleAccess(new PorterVehicleAccessDB());

            //  updating the ServiceInterestGradeDB objects
            foreach ($porter->getServiceInterestGrades() as $interest) {
                $service = $interest->getService();
                if ($service !== null)
                    $interest->setServiceId($service->getId());
            }
                
        }

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('update_form', $entities);

        return $this->render('porter/updatePorters.html.twig', array('listForm' => $listForm->createView()));
    }

    //========================================================================================
    //  Misc
    //========================================================================================

    protected function getBlankPorterForInsert() {
        //  get services
        $dbServices = $this->get('app.database')->getServices(false);

        //  create blank PortersDB entity
        $nPorter = new PortersDB();

        //  add 3 PorterVehicleAccessDB entities to blank PortersDB entity
        for ($i = 0; $i < 3; $i++)
            $nPorter->addVehicleAccess(new PorterVehicleAccessDB());
        
        //  add ServiceInterestGradeDB entities to blank PortersDB entity
        foreach ($dbServices as $service) {
            $interest = new ServiceInterestGradeDB();
            $interest->setService($service);
            $nPorter->addServiceInterestGrade($interest);
        }

        return $nPorter;
    }
    
}