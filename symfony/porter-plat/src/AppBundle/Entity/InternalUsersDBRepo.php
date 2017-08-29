<?php

// src/AppBundle/Entity/InternalUsersDBRepo.php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class InternalUsersDBRepo extends EntityRepository implements UserLoaderInterface {

	public function loadUserByUsername($username_) {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username_)
            ->getQuery()
            ->getOneOrNullResult();
    }

}