<?php

// src/AppBundle/Form/Request/PorterRequestActivitySummaryUpdateType.php
namespace AppBundle\Form\Request;

use AppBundle\Services\RequestOperations;
use AppBundle\Services\Database;
use Doctrine\ORM\EntityRepository;
use AppBundle\Form\Request\PorterRequestActivityUpdateType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PorterRequestActivitySummaryUpdateType extends AbstractType {

	private $requestOperations;
	private $database;

	public function __construct(RequestOperations $requestOperations_, Database $database_) {
		$this->requestOperations = $requestOperations_;
		$this->database = $database_;
	}

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event_) {
				
				$choices = [];
				$form = $event_->getForm();

		        $activitySummary = $event_->getData();

		        if ($activitySummary !== null) {
			        $request = $activitySummary->getRequest();
			        $choices = $this->requestOperations->getPossiblePortersForRequest($request, true, true);
		    	}

				$form->add('porter', EntityType::class, [
					'class'			=> 'AppBundle:PortersDB',
            		'choice_label'	=> 'fullNameAndCity',
            		'label'			=> false,
            		'required'		=> false,
            		'choices'		=> $choices
				]);
			})
			->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event_) {
				
				$choices = [];
				$form = $event_->getForm();

				//	getting submitted data
		        $submitData = $event_->getData();
		        //	getting request
		        $request = $this->database->getRequest($submitData['requestId']);

		        //	validation hack - porter that was been passed in is used to build the choices array
		        if ($submitData !== null)
			        $choices = $this->requestOperations->getPossiblePortersForRequest($request, true, true);

				$form->add('porter', EntityType::class, [
					'class'			=> 'AppBundle:PortersDB',
            		'choice_label'	=> 'fullNameAndCity',
            		'label'			=> false,
            		'required'		=> false,
            		'choices'		=> $choices
				]);
			})
			->add('requestId', HiddenType::class, [
        		'label'			=> false
			])
			->add('startActivity', PorterRequestActivityUpdateType::class, [
        		'label'			=> false
			])
			->add('endActivity', PorterRequestActivityUpdateType::class, [
        		'label'			=> false
			]);

			$builder_->get('requestId')
				->addModelTransformer(new CallbackTransformer(
					function ($toFront_) {
						return $toFront_;
					},
					function ($fromFront_) {
						if ($fromFront_ !== null)
							return intval($fromFront_);
						return $fromFront_;
					}
				));
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\PorterRequestActivitySummary']);
	}

}