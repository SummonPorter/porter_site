<?php

// src/AppBundle/Form/ServiceInterestGradeType.php
namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceInterestGradeType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('serviceId', HiddenType::class, [
				'label' 		=> false
			])
			->add('porterId', HiddenType::class, [
				'label' 		=> false
			])
			->add('service', HiddenType::class, [
				'label' 		=> false
			])
			->add('interestGrade', ChoiceType::class, [
				'choices' 		=> [
					'(5) the highest priority in my life'		=> 5,
					'(4) if you need me I\'ll be there'  		=> 4,
					'(3) if my schedule is free I\'ll take it' 	=> 3,
					'(2) now and again but not often' 			=> 2,
					'(1) maybe once a year' 					=> 1,
					'(0) not a chance, ever'					=> 0,
				],
				'label' 		=> false
			]);

		$builder_->get('serviceId')
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
		$builder_->get('service')
			->addModelTransformer(new CallbackTransformer(
				function ($toFront_) {
					if ($toFront_ !== null)
						return $toFront_->getName();
					return $toFront_;
				},
				function ($fromFront_) {
					return null;
				}
			));
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\ServiceInterestGradeDB']);
	}

}