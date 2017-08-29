<?php

// src/AppBundle/Security/CustomerAuthenticator.php
namespace AppBundle\Security;

use AppBundle\Entity\CustomerUsersDB;
use AppBundle\Entity\CustomerLoginActivityDB;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\Router;
use Symfony\Bridge\Monolog\Logger;

class CustomerAuthenticator extends AbstractGuardAuthenticator {

	private $doctrine;
	private $logger;
	private $router;
	private $encoder;

	//	handler constructor
	public function __construct(Registry $doctrine_, Logger $logger_, Router $router_, UserPasswordEncoder $encoder_) {
		$this->doctrine = $doctrine_;
		$this->logger = $logger_;
		$this->router = $router_;
		$this->encoder = $encoder_;
		date_default_timezone_set('UTC');
		$this->logger->debug('[Customer Authenticator] constructor');
	}

	/**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request_) {
    	$this->logger->debug('[Customer Authenticator] calling getCredentials');

        $userName = 'test@tester.com';
        $password = 'test_password';

        // What you return here will be passed to getUser() as $credentials
        return ['userName' => $userName, 'password' => $password];
    }

    //	$credentials_ created in getCredentials
    public function getUser($credentials_, UserProviderInterface $userProvider_) {
    	$this->logger->debug('[Customer Authenticator] getting user: ' . print_r($credentials_, true));

        if (empty($credentials_) || $userProvider_ === null)
        	return null;

        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $userProvider_->loadUserByUsername($credentials_['userName']);
    }

    public function checkCredentials($credentials_, UserInterface $user_) {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        $this->logger->debug('[Customer Authenticator] checking credentials: ' . print_r($credentials_, true));

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request_, TokenInterface $token_, $providerKey_) {
        $this->logger->debug('[Customer Authenticator] authentication succeeded');

        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request_, AuthenticationException $exception_) {
    	$this->logger->debug('[Customer Authenticator] authentication failed: ' . strtr($exception_->getMessageKey(), $exception_->getMessageData()));
        $data = [
            'inputDeclaredValid' => false,
            'message' => strtr($exception_->getMessageKey(), $exception_->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    //	Called when authentication is needed, but it's not sent
    public function start(Request $request_, AuthenticationException $authException_ = null) {
    	$this->logger->debug('[Customer Authenticator] start');

        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe() {
        return true;
    }
	

}