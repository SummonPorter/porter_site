<?php

// src/AppBundle/Form/Request/ActiveUpdateRequestEntityType.php
namespace AppBundle\Form\Request;

use AppBundle\Services\RequestOperations;
use AppBundle\Services\Database;
use AppBundle\Form\Request\RequestAddressType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

use \DateTime;
use \DateTimeZone;

class ActiveUpdateRequestEntityType extends AbstractType {

	private $requestOperations;
	private $database;

	public function __construct(RequestOperations $requestOperations_, Database $database_) {
		$this->requestOperations = $requestOperations_;
		$this->database = $database_;
	}

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('rId', HiddenType::class, [
				'label' 		=> false
			])
			->add('rUpdateStartEpoch', HiddenType::class, [
				'label' 		=> false
			])
			->add('rDiscount', EntityType::class, [
				'class'			=> 'AppBundle:DiscountsDB',
				'choice_label'	=> 'name',
				'multiple'		=> false,
				'required'		=> false,
				'label'			=> false
			])
			->add('rPortersWanted', IntegerType::class, [
				'label' 		=> false
			])
			->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event_) {
				
				$choices = [];
				$form = $event_->getForm();

		        $activeRequestEntity = $event_->getData();

		        if ($activeRequestEntity !== null) {
			        $request = $this->database->getRequest($activeRequestEntity->getRId(), true);
			        $porters = $this->requestOperations->getPossiblePortersForRequest($request, true);

			        foreach ($porters as $porter)
			        	$choices[$porter->getFullNameAndCity()] = $porter->getId();
		    	}

				$form->add('rPorterAssignments', CollectionType::class, [
					'allow_add'		=> true,
            		'by_reference'	=> false,
            		'label'			=> false,
					'entry_type'	=> ChoiceType::class,
					'entry_options' => [
						'choices'		=> $choices,
						'multiple'		=> false,
						'required'		=> false,
						'label'			=> false
					]
				]);
			})
			->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event_) {
				
				$choices = [];
				$form = $event_->getForm();

		        $submitData = $event_->getData();
		        
		        //	validation hack - any porter ids that have been passed in are used to build the choices array
		        if ($submitData !== null) {
			        foreach ($submitData['rPorterAssignments'] as $porterId) {
			        	if (strlen($porterId) > 0)
			        		$choices[$porterId] = intval($porterId);
			        }
		    	}

				$form->add('rPorterAssignments', CollectionType::class, [
					'allow_add'		=> true,
            		'by_reference'	=> false,
            		'label'			=> false,
					'entry_type'	=> ChoiceType::class,
					'entry_options' => [
						'choices'		=> $choices,
						'multiple'		=> false,
						'required'		=> false,
						'label'			=> false
					]
				]);
			})
			->add('rStartDateTime', DateTimeType::class, [
				'label' 		=> false,
				'format'		=> 'yyyyMMddhh:mm:ss'
			])
			->add('rEstDuration', IntegerType::class, [
				'label' 		=> false
			])
			->add('rDetails', TextareaType::class, [
				'label' 		=> false
			])
			->add('rAddresses', CollectionType::class, [
				'allow_add'		=> true,
        		'by_reference'	=> false,
        		'label'			=> false,
				'entry_type'	=> RequestAddressType::class
			]);

		$builder_->get('rId')
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
		$builder_->get('rUpdateStartEpoch')
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\ActiveUpdateRequestEntity']);
	}

}