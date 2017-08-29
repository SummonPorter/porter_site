<?php

// src/AppBundle/Entity/CmsConfig.php
namespace AppBundle\Entity;

class CmsConfig {

	protected $doctrinePath;
	protected $entityClass;
	protected $mainFormType;
	protected $insertFormType;
	protected $mainPageRoute;
	protected $insertPageRoute;

	public function __construct($doctrinePath_, $entityClass_, $mainFormType_, $insertFormType_, $mainPageRoute_, $insertPageRoute_) {
		$this->doctrinePath = $doctrinePath_;
		$this->entityClass = $entityClass_;
		$this->mainFormType = $mainFormType_;
		$this->insertFormType = $insertFormType_;
		$this->mainPageRoute = $mainPageRoute_;
		$this->insertPageRoute = $insertPageRoute_;
	}

	public function getDoctrinePath() {
		return $this->doctrinePath;
	}

	public function getEntityClass() {
		return $this->entityClass;
	}

	public function getMainFormType() {
		return $this->mainFormType;
	}

	public function getInsertFormType() {
		return $this->insertFormType;
	}

	public function getMainPageRoute() {
		return $this->mainPageRoute;
	}

	public function getInsertPageRoute() {
		return $this->insertPageRoute;
	}

}