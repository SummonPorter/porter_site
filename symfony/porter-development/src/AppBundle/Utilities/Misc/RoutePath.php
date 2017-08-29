<?php

// src/AppBundle/Utilities/Misc/RoutePath.php
namespace AppBundle\Utilities\Misc;

class RoutePath {

	protected $routeName;
	protected $routeParameters;

	public function __construct(string $routeName_, Array $routeParameters_ = []) {
		$this->routeName = $routeName_;
		$this->routeParameters = $routeParameters_;
	}

	public function getRouteName(): string {
		return $this->routeName;
	}

	public function getRouteParameters(): Array {
		return $this->routeParameters;
	}

	public function addRouteParameter(string $key_, string $value_) {
		$this->routeParameters[$key_] = $value_;
	}

}