<?php

// src/AppBundle/Security/InternalUsersHandler.php
namespace AppBundle\Security;

use AppBundle\Entity\InternalUsersDB;
use AppBundle\Entity\InternalLoginActivityDB;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Monolog\Logger;

class InternalUsersHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface {

	private $doctrine;
	private $logger;
	private $router;
	//private $security;

	//public function __construct(Registry $doctrine_, Logger $logger_, Router $router_, SecurityContext $security_) {
	public function __construct(Registry $doctrine_, Logger $logger_, Router $router_) {
		$this->doctrine = $doctrine_;
		$this->logger = $logger_;
		$this->router = $router_;
		//$this->security = $security_;
		date_default_timezone_set('UTC');
	}

	public function onAuthenticationSuccess(Request $request_, TokenInterface $token_) {
		//if ($this->security->isGranted('ROLE_ADMIN'))
		$username = $this->getUsernameFromToken($token_);

		if ($username !== null)
			$this->createInternalLoginActivity($username, 'login', true, $request_);
		
		return new RedirectResponse($this->router->generate('login_controller_main'));
	}

	public function onAuthenticationFailure(Request $request_, AuthenticationException $AuthenticationException_) {
		$username = $this->getUsernameFromToken($AuthenticationException_->getToken());

		if ($username !== null)
			$this->createInternalLoginActivity($username, 'login', false, $request_);

		$request_->getSession()->set(Security::AUTHENTICATION_ERROR, $AuthenticationException_);

		return new RedirectResponse($this->router->generate('login_controller_main'));
	}

	public function onLogoutSuccess(Request $request_) {
		return new RedirectResponse($this->router->generate('login_controller_main'));
	}

	private function getUsernameFromToken(TokenInterface $token_) {
		$user = $token_->getUser();

		if (is_string($user))
			return $user;
		elseif ($user instanceof UserInterface)
			return $user->getUsername();
		elseif ($user !== null)
			return $user->toString();

		return null;
	}

	private function createInternalLoginActivity($username_, $activity_, $success_, Request $request_) {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('AppBundle:InternalUsersDB', 'u')
            ->where('u.username = ?1')
            ->setParameter(1, $username_);

        try {
        	$user = $qb->getQuery()->getSingleResult();

        	$internalLoginActivity = new InternalLoginActivityDB();
        	$internalLoginActivity->setInternalUser($user);
        	$internalLoginActivity->setActivity($activity_);
        	$internalLoginActivity->setSuccess($success_);
        	$internalLoginActivity->setIp($request_->getClientIp());
        	$internalLoginActivity->setActivityEpoch(round(microtime(true) * 1000));

        	$em->persist($internalLoginActivity);
        	$em->flush();
        }
        catch (NoResultException $e_) {
        }

	}
	

}