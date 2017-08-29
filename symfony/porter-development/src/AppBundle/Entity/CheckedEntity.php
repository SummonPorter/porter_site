<?php

// src/AppBundle/Entity/CheckedEntity.php
namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class CheckedEntity {

	/**
	* @Assert\Type("bool")
	*/
	protected $checked;

	/**
	* @Assert\Type("integer")
	*/
	protected $id;

	/**
	* @Assert\Type("string")
	*/
	protected $doctrinePath;

	/**
	* @Assert\Type("string")
	*/
	protected $entityClass;

	protected $entity;

	public function __construct($doctrinePath_ = '', $entityClass_ = '', $entity_ = null) {
		$this->checked = false;
		$this->doctrinePath = $doctrinePath_;
		$this->setEntityClass($entityClass_);
		$this->setEntity($entity_);
	}

	public function getChecked() {
		return $this->checked;
	}

	public function setChecked($checked_) {
		$this->checked = $checked_;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id_) {
		$this->id = $id_;
	}

	public function getDoctrinePath() {
		return $this->doctrinePath;
	}

	public function setDoctrinePath($doctrinePath_) {
		$this->doctrinePath = $doctrinePath_;
	}

	public function getEntityClass() {
		return $this->entityClass;
	}

	public function setEntityClass($entityClass_) {
		$this->entityClass = $entityClass_;
	}

	public function getEntity() {
		return $this->entity;
	}

	public function setEntity($entity_) {
		$this->entity = $entity_;
		if ($entity_ !== null)
			$this->setId($entity_->getId());
	}

}