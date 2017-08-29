<?php

// src/AppBundle/Form/Request/RequestsProcessType.php
namespace AppBundle\Form\Request;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestsProcessType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('requestId', HiddenType::class, [
				'label' => false
			])
			->add('newState', HiddenType::class, [
				'label' => false
			])
			->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event_) {
				$form = $event_->getForm();
		        $requestProcess = $event_->getData();

		        if ($requestProcess === null) {
		        	$form->add('process', SubmitType::class, [
						'label' => 'button'
					]);
		        }
		        else {
		        	$form->add('process', SubmitType::class, [
						'label' => $requestProcess->getSubmitText()
					]);
		        }
			});

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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\RequestProcess']);
	}

}