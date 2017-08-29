<?php

// src/AppBundle/Security/CustomerUsersHandler.php
namespace AppBundle\Security;

use AppBundle\Entity\CustomerUsersDB;
use AppBundle\Entity\CustomerLoginActivityDB;
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

class CustomerUsersHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface {

	private $doctrine;
	private $logger;
	private $router;

	//	handler constructor
	public function __construct(Registry $doctrine_, Logger $logger_, Router $router_) {
		$this->doctrine = $doctrine_;
		$this->logger = $logger_;
		$this->router = $router_;
		date_default_timezone_set('UTC');
	}

	//	called when customer has attempted to login and has succeeded
	public function onAuthenticationSuccess(Request $request_, TokenInterface $token_) {
		$username = $this->getUsernameFromToken($token_);
		$this->logger->debug('customer (' . $username . ') logged in');

		if ($username !== null)
			$this->createCustomerLoginActivity($username, 'login', true, $request_);
		
		return new RedirectResponse($this->router->generate('login_controller_main'));
	}

	//	called when customer has attempted to login but failed
	public function onAuthenticationFailure(Request $request_, AuthenticationException $AuthenticationException_) {
		$username = $this->getUsernameFromToken($AuthenticationException_->getToken());
		$this->logger->debug('customer (' . $username . ') failed to log in');

		if ($username !== null)
			$this->createCustomerLoginActivity($username, 'login', false, $request_);

		$request_->getSession()->set(Security::AUTHENTICATION_ERROR, $AuthenticationException_);

		return new RedirectResponse($this->router->generate('login_controller_main'));
	}

	//	called when logging out
	public function onLogoutSuccess(Request $request_) {
		return new RedirectResponse($this->router->generate('login_controller_main'));
	}

	//	attempts to return the customer's username
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

	//	logs login activity
	private function createCustomerLoginActivity($username_, $activity_, $success_, Request $request_) {
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:CustomersDB', 'c')
            ->where('c.email = ?1')
            ->setParameter(1, $username_);

        try {
        	$user = $qb->getQuery()->getSingleResult();

        	$customerLoginActivity = new CustomerLoginActivityDB();
        	$customerLoginActivity->setCustomer($user);
        	$customerLoginActivity->setActivity($activity_);
        	$customerLoginActivity->setSuccess($success_);
        	$customerLoginActivity->setIp($request_->getClientIp());
        	$customerLoginActivity->setActivityEpoch(round(microtime(true) * 1000));

        	$em->persist($customerLoginActivity);
        	$em->flush();
        }
        catch (NoResultException $e_) {
        }

	}
	

}