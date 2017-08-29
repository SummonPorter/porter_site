<?php

// src/AppBundle/Security/InternalUsersProvider.php
namespace AppBundle\Security;

use AppBundle\Entity\InternalUsersDB;
use AppBundle\Entity\InternalLoginActivityDB;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Doctrine\ORM\NoResultException;

class InternalUsersProvider implements UserProviderInterface {

	private $doctrine;
    private $logger;

    public function __construct(Registry $doctrine_, Logger $logger_) {
        $this->doctrine = $doctrine_;
        $this->logger = $logger_;
    }

	public function loadUserByUsername($username_) {
		//	getting user
		$em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('AppBundle:InternalUsersDB', 'u')
            ->where('u.username = ?1')
            ->setParameter(1, $username_);

        $userEntity = $qb->getQuery()->getSingleResult();

        try {
            //	check to see if user is locked (3 failed login attempts over the past 30 minutes)
            $qb = $em->createQueryBuilder();
            $qb->select('a')
                ->from('AppBundle:InternalLoginActivityDB', 'a')
                ->where('a.internalUser = ?1')
                ->where('a.activity = \'login\'')
                ->orderBy('a.activityEpoch', 'DESC')
                ->setMaxResults(3)
                -setParameter(1, $userEntity->getId());

            $loginActivityResults = $qb->getQuery()->getResult();

            //  only proceed if there are 3 failed attempts
            if (count($loginActivityResults) < 3)
                return $userEntity;

            $allFails = true;
            foreach ($loginActivityResults as $loginActivity)
                $allFails = $allFails && $loginActivity->getSuccess();
            
            //  if all login attempts were failures
            if (!$allFails) {
                $timeDifference = $loginActivityResults[0]->getActivityEpoch() - $loginActivityResults[2]->getActivityEpoch();
                //  convert to seconds
                $timeDifference = round($timeDifference / 1000);
                //  if time difference between attempts is 60 minutes or less, lock account
                $userEntity->setAccountLocked($timeDifference < (60 * 60));
            }

            return $userEntity;
        }
        catch (NoResultException $e_) {
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username_)
        );
    }

    public function refreshUser(UserInterface $user_) {
        if (!$user_ instanceof InternalUsersDB) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user_))
            );
        }

        return $this->loadUserByUsername($user_->getUsername());
    }

    public function supportsClass($class_) {
        return $class_ === 'AppBundle\Entity\InternalUsersDB';
    }

}