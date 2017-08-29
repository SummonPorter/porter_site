<?php

// src/AppBundle/Controller/LoginController.php
namespace AppBundle\Controller;

use AppBundle\Controller\GlobalController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends GlobalController {

    public function __construct() {
        $this->standardConfiguration();
    }
   
    /**
    * @Route("/login", name="login_controller_main")
    */
    public function mainAction(Request $request_) {
        $authUtils = $this->get('security.authentication_utils');

        //  get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
        //  last username entered by the user
        $lUsername = $authUtils->getLastUsername();

        $lastLoginDateTime = null;
        $lastLoginAddress = null;

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            $qb->select('a')
                ->from('AppBundle:InternalLoginActivityDB', 'a')
                ->where('a.internalUser = ' . $this->getUser()->getId())
                ->where('a.activity = \'login\'')
                ->where('a.success = ' . true)
                ->orderBy('a.activityEpoch', 'DESC')
                ->setMaxResults(2);

            $loginActivityResults = $qb->getQuery()->getResult();
            if ($loginActivityResults !== null) {
                if (count($loginActivityResults) == 2) {
                    $lastLoginDateTime = $loginActivityResults[1]->getActivityEpochString();
                    if ($loginActivityResults[1]->getIp() !== $loginActivityResults[0]->getIp())
                        $lastLoginAddress = $loginActivityResults[1]->getIp() . ' (different address)';
                    else
                        $lastLoginAddress = $loginActivityResults[1]->getIp() . ' (same address)';
                }
            }
        }


        return $this->render('login.html.twig', ['lastUsername' => $lUsername, 'error' => $error, 'lastLoginDateTime' => $lastLoginDateTime, 'lastLoginAddress' => $lastLoginAddress]);
    }

}