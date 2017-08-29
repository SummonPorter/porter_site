<?php

// src/AppBundle/Controller/DiscountsController.php
namespace AppBundle\Controller;

use AppBundle\Controller\CmsController;
use AppBundle\Form\CheckedEntitiesDeleteListType;
use AppBundle\Form\ListInsertType;
use AppBundle\Form\Discount\DiscountsListInsertType;
use AppBundle\Form\Discount\DiscountsListUpdateType;
use AppBundle\Entity\DiscountsDB;
use AppBundle\Services\CmsOperations;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DiscountsController extends CmsController {

    public function __construct() {
        parent::__construct();
    }

    //========================================================================================
    //  Setup
    //========================================================================================

    protected function setupCmsOperations() {
        $this->get('app.cms_operations')->setup('AppBundle:DiscountsDB', CitiesDB::class, new RoutePath('discounts_controller_main'));
        $this->addFormHandler('main_form',          'main_form',            CheckedEntitiesDeleteListType::class,   new RoutePath('discounts_controller_main'));
        $this->addFormHandler('main_insert_form',   'main_insert_form',     ListInsertType::class,                  new RoutePath('discounts_controller_insert'));
        $this->addFormHandler('insert_form',        'insert_form',          DiscountsListInsertType::class,         new RoutePath('discounts_controller_main'));
        $this->addFormHandler('update_form',        'update_form',          DiscountsListUpdateType::class,         new RoutePath('discounts_controller_main'));
    }

    //========================================================================================
    //  Actions
    //========================================================================================

	/**
	* @Route("/discounts", name="discounts_controller_main")
	*/
	public function mainAction(Request $request_) {
        $this->setupCmsOperations();

        //  may redirect depending on $request_ / return
        $redirect = $this->manageSubmits($request_);
        if ($redirect !== null)
            return $redirect;

        //  get resources from CmsController
        $resources = $this->buildPageResources();

        return $this->render('discount/discounts.html.twig', array('listForm' => $resources['listForm']->createView(), 'insertForm' => $resources['insertForm']->createView(), 'pageMarks' => $resources['pageMarks']));
	}

	/**
	* @Route("/discounts/insert/{count_}", name="discounts_controller_insert", requirements={"count_": "\d+"}))
	*/
	public function insertAction(Request $request_, int $count_) {
        $this->setupCmsOperations();

        //  creating blank entities
        $entities = [];
        for ($i = 0; $i < $count_; $i++)
            $entities[] = new DiscountsDB();

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('insert_form', $entities);

		return $this->render('discount/addDiscounts.html.twig', array('listForm' => $listForm->createView()));
	}

    /**
    * @Route("/discounts/details/{id_}", name="discounts_controller_details", requirements={"id_": "\d+"}))
    */
    public function detailsAction(Request $request_, int $id_) {
        $this->setupCmsOperations();

        $discount = $this->get('app.database')->getDiscount($id_);
        return $this->render('discount/detailsDiscounts.html.twig', array('discount' => $discount));
    }

    /**
    * @Route("/discounts/update/{id_}", name="discounts_controller_update", requirements={"id_": "\d+"}))
    */
    public function updateAction(Request $request_, int $id_) {
        $this->setupCmsOperations();
        
        //  creating array to put entity into
        $entities = [$this->get('app.database')->getDiscount($id_)];

        //  building insert list form
        $listForm = $this->getCmsOps()->buildForm('update_form', $entities);

        return $this->render('discount/updateDiscounts.html.twig', array('listForm' => $listForm->createView()));
    }

    //  method called in case special operations are needed when updating or deleting data
    /*protected function specialUpdateOps(Request $request_, $result_) {
        $entities = $result_['data']->getEntities();
        foreach ($entities as $e) {
            if ($e->getChecked()) {
                if ($e->getDiscountType() === 'percentage' && $e->getDiscount() > 99)
                    $e->setDiscount(25);
            }
        }
    }

    //  method called in case special operations are needed when inserting data
    protected function specialInsertOps(Request $request_, $result_) {
        $entities = $result_['data']->getEntities();
        foreach ($entities as $e) {
            if ($e->getChecked()) {
                if ($e->getDiscountType() === 'percentage' && $e->getDiscount() > 99)
                    $e->setDiscount(25);
            }
        }
    }*/

}