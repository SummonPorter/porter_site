<?php

// src/AppBundle/Form/Discount/DiscountType.php
namespace AppBundle\Form\Discount;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

use \DateTime;
use \DateTimeZone;

class DiscountType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('code', TextType::class, [
				'label' 		=> false
			])
			->add('name', TextType::class, [
				'label' 		=> false
			])
			->add('description', TextareaType::class, [
				'label' 		=> false
			])
			->add('startEpoch', DateTimeType::class, [
				'label' 		=> false,
				'input'			=> 'timestamp',
				'format'		=> 'yyyyMMddhh:mm:ss'
			])
			->add('endEpoch', DateTimeType::class, [
				'label' 		=> false,
				'input'			=> 'timestamp',
				'format'		=> 'yyyyMMddhh:mm:ss'
			])
			->add('discount', IntegerType::class, [
				'label' 		=> false
			])
			->add('discountType', ChoiceType::class, [
				'choices' 		=> [
					'percentage'=> 'percentage',
					'flat' 		=> 'flat'
				],
				'label' 		=> false
			])
			->add('whitelistServices', EntityType::class, [
				'class'			=> 'AppBundle:ServicesDB',
				'choice_label'	=> 'name',
				'multiple'		=> true,
				'required'		=> true,
				'label'			=> false
			])
			->add('blacklistCountries', EntityType::class, [
				'class'			=> 'AppBundle:CountriesDB',
				'choice_label'	=> 'name',
				'multiple'		=> true,
				'required'		=> false,
				'label'			=> false
			])
			->add('blacklistRegions', EntityType::class, [
				'class'			=> 'AppBundle:RegionsDB',
				'choice_label'	=> 'name',
				'multiple'		=> true,
				'required'		=> false,
				'label'			=> false
			])->add('blacklistCities', EntityType::class, [
				'class'			=> 'AppBundle:CitiesDB',
				'choice_label'	=> 'name',
				'multiple'		=> true,
				'required'		=> false,
				'label'			=> false
			]);

		/*$builder_->get('startEpoch')
			->addModelTransformer(new CallbackTransformer(
				function ($toFront_) {
					if ($toFront_ !== null)
						return DateTime::createFromFormat('U', $toFront_);
					return $toFront_;
				},
				function ($fromFront_) {
					if ($fromFront_ !== null)
						return intval($fromFront_->format('U'));
					return $fromFront_;
				}
			));
		$builder_->get('endEpoch')
			->addModelTransformer(new CallbackTransformer(
				function ($toFront_) {
					if ($toFront_ !== null)
						return DateTime::createFromFormat('U', $toFront_);
					return $toFront_;
				},
				function ($fromFront_) {
					if ($fromFront_ !== null)
						return intval($fromFront_->format('U'));
					return $fromFront_;
				}
			));
		$builder_->get('createdEpoch')
			->addModelTransformer(new CallbackTransformer(
				function ($toFront_) {
					return $toFront_;
				},
				function ($fromFront_) {
					if ($fromFront_ === null)
						return time();
					return intval($fromFront_);
				}
			));*/
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\DiscountsDB']);
	}

}