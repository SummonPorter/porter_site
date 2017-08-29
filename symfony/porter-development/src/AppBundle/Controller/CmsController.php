<?php

// src/AppBundle/Controller/CmsController.php
namespace AppBundle\Controller;

use AppBundle\Controller\GlobalController;
use AppBundle\Entity\CheckedEntity;
use AppBundle\Services\CmsOperations;
use AppBundle\Services\FormHandlerFactory;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CmsController extends GlobalController {

    public function __construct() {
        $this->standardConfiguration();
    }

    //========================================================================================
    //  Resource Building
    //========================================================================================

    //  builds resources to be rendered in template pages
    protected function buildPageResources($option_ = null) {
        $resources = [];

        //  get entity data from DB
        $dbEntities = $this->get('app.database')->getEntitiesFor($this->getCmsOps()->getDoctrinePath());
        //  get count of entities in DB
        $dbEntitiesCount = count($dbEntities);

        //  building main list data
        $checkedEntities = [];
        foreach ($dbEntities as $entity)
            $checkedEntities[] = new CheckedEntity($this->getCmsOps()->getDoctrinePath(), $this->getCmsOps()->getEntityClass(), $entity);

        //  building insert list form
        $resources['insertForm']    = $this->getCmsOps()->buildForm('main_insert_form', null);
        //  building main form
        $resources['listForm']      = $this->getCmsOps()->buildForm('main_form', $checkedEntities);
        //  building page marks
        $resources['pageMarks']     = $this->getCmsOps()->buildPageMarks($dbEntitiesCount, $this->getCurrentPage(1, $dbEntitiesCount));

        return $resources;
    }

    //========================================================================================
    //  Managing Form Submits
    //========================================================================================

	protected function manageSubmits(Request $request_) {
        return $this->get('app.cms_operations')->manageSubmits($request_);
    }

    //========================================================================================
    //  Misc
    //========================================================================================

    //  calculates page based on count from db query
    protected function getCurrentPage(int $page_, int $dbCount_): int {
        return $page_;
    }

    protected function getCmsOps() {
        return $this->get('app.cms_operations');
    }

    protected function getFHFactory() {
        return $this->get('app.form_handler_factory');
    }

    protected function addFormHandler(string $handlerKey_, string $handlerType_, string $formClass_, RoutePath $redirectPath_) {
        $this->getCmsOps()->addFormHandler($this->getFHFactory()->getHandler($handlerType_, $formClass_, $redirectPath_), $handlerKey_);
    }

}