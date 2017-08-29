<?php

// src/AppBundle/Utilities/FormHandler/FormHandler.php
namespace AppBundle\Utilities\FormHandler;

use AppBundle\Services\CmsOperations;
use AppBundle\Entity\EntitiesList;
use AppBundle\Utilities\Misc\RoutePath;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

abstract class FormHandler {

	protected $formClass;
	protected $redirectPath;
	protected $sourcePath;

	public function __construct(string $formClass_, RoutePath $redirectPath_) {
		$this->formClass = $formClass_;
		$this->redirectPath = $redirectPath_;
	}

	public function getFormClass(): string {
		return $this->formClass;
	}

	public function getRedirectPath(): RoutePath {
		return $this->redirectPath;
	}

	//	builds the form and returns it
	abstract public function buildForm(CmsOperations $cmsOperations_, Array $entities_ = null);

	//	processes submit and returns a redirect response if needed
	//	may also create and add new flash messages to session
	abstract public function manageSubmit(CmsOperations $cmsOperations_, Request $request_, Array $results_);

	//	generates a url using redirectPath
	protected function generateUrl(CmsOperations $cmsOperations_, Request $request_) {
		$url = $cmsOperations_->getRouter()->generate($this->getRedirectPath()->getRouteName(), $this->getRedirectPath()->getRouteParameters(), UrlGeneratorInterface::ABSOLUTE_PATH);
		$redirectResponse = new RedirectResponse($url);
        $redirectResponse->prepare($request_);
        return $redirectResponse;
	}

	protected function addSuccessMessage(CmsOperations $cmsOperations_, SessionInterface $session_, string $message_) {
		$this->addFlashMessage('successes', $message_, $session_->getFlashBag());
	}

	protected function addErrorMessage(CmsOperations $cmsOperations_, SessionInterface $session_, string $message_) {
		$this->addFlashMessage('errors', $message_, $session_->getFlashBag());
	}

	protected function addWarningMessage(CmsOperations $cmsOperations_, SessionInterface $session_, string $message_) {
		$this->addFlashMessage('warnings', $message_, $session_->getFlashBag());
	}

	private function addFlashMessage(string $key_, string $message_, FlashBag $flashBag_) {
		if ($flashBag_ !== null)
			$flashBag_->add($key_, $message_);
	}

}