<?php

// src/AppBundle/Controller/GlobalController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GlobalController extends Controller {

    protected function standardConfiguration() {
        date_default_timezone_set('UTC');
    }

}