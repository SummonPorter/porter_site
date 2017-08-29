<?php

// src/AppBundle/Form/PorterVehicleAccessType.php
namespace AppBundle\Form\Porter;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PorterVehicleAccessType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('porterId', HiddenType::class, [
				'label' 		=> false
			])
			->add('vehicleType', ChoiceType::class, [
				'choices' 		=> [
					''						=> null,
					'motorcycle'			=> 'motorcycle',
					'sedan'					=> 'sedan',
					'truck'  				=> 'truck',
					'suv' 					=> 'suv',
					'van' 					=> 'van'
				],
				'label' 		=> false
			])
			->add('personal', ChoiceType::class, [
            	'label' 		=> false,
            	'choices'		=> [
            		''			=> null,
            		'yes'		=> true,
            		'no'		=> false
            	]
            ]);

		$builder_->get('id')
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
		$builder_->get('porterId')
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\PorterVehicleAccessDB']);
	}

}