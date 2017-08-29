<?php

// src/AppBundle/Form/Porter/PorterType.php
namespace AppBundle\Form\Porter;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Form\Porter\PorterVehicleAccessType;
use AppBundle\Form\ServiceInterestGradeType;

use \DateTime;
use \DateTimeZone;

class PorterType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('firstName', TextType::class, [
				'label' 		=> false
			])
			->add('lastName', TextType::class, [
				'label' 		=> false
			])
			->add('birthDate', BirthdayType::class, [
				'label' 		=> false,
				'required'		=> false,
				'format'		=> 'yyyyMMdd'
			])
			->add('mobilePhone', TextType::class, [
				'label' 		=> false
			])
			->add('homePhone', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('email', EmailType::class, [
				'label' 		=> false
			])
			->add('address', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('mailing', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('city', EntityType::class, [
				'class'			=> 'AppBundle:CitiesDB',
				'choice_label'	=> 'nameAndDetails',
				'multiple'		=> false,
				'required'		=> true,
				'label'			=> false
			])
			->add('backgroundRef', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('signupEpoch', DateType::class, [
				'label' 		=> false,
				'required'		=> true,
				'input'			=> 'timestamp',
				'format'		=> 'yyyyMMdd'
			])
			->add('active', ChoiceType::class, [
				'choices' 		=> [
					'yes' 		=> true,
					'no' 		=> false
				],
				'label' 		=> false
			])
			->add('vehicles', CollectionType::class, [
            	'entry_type'	=> PorterVehicleAccessType::class,
            	'allow_add'		=> true,
            	'by_reference'	=> false,
            	'label'			=> false
            ])
			->add('serviceInterestGrades', CollectionType::class, [
            	'entry_type'	=> ServiceInterestGradeType::class,
            	'allow_add'		=> true,
            	'by_reference'	=> false,
            	'label'			=> false
            ]);

		$builder_->get('signupEpoch')
			->addModelTransformer(new CallbackTransformer(
				function ($toFront_) {
					if ($toFront_ === null)
						return time();
					return $toFront_;
				},
				function ($fromFront_) {
					if ($fromFront_ !== null)
						return intval($fromFront_);
					return $fromFront_;
				}
			));
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
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\PortersDB']);
	}

}