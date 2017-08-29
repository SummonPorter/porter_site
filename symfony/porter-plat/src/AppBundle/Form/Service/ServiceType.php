<?php

// src/AppBundle/Form/Service/ServiceType.php
namespace AppBundle\Form\Service;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options_) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('name', TextType::class, [
				'label' 		=> false
			])
			->add('description', TextareaType::class, [
				'label' 		=> false
			])
			->add('addressCount', IntegerType::class, [
				'label' 		=> false
			])
			->add('feeRate', ChoiceType::class, [
				'choices' 		=> [
					'by minute' => 'minute',
					'by hour' 	=> 'hour'
				],
				'label' 		=> false
			])
			->add('fee', MoneyType::class, [
				'label' 		=> false,
				'currency'		=> false,
				'scale'			=> 3
			])
			->add('baseFee', MoneyType::class, [
				'label' 		=> false,
				'currency'		=> false,
				'scale'			=> 3
			])
			->add('pay', MoneyType::class, [
				'label' 		=> false,
				'currency'		=> false,
				'scale'			=> 3
			])
			->add('trustFee', MoneyType::class, [
				'label' 		=> false,
				'currency'		=> false,
				'scale'			=> 3
			])
			->add('defaultEstDuration', IntegerType::class, [
				'label' 		=> false
			])
			->add('webHeader', TextType::class, [
				'label' 		=> false
			])
			->add('webMobileHeader', TextType::class, [
				'label' 		=> false
			])
			->add('webIcon', TextType::class, [
				'label' 		=> false
			])
			->add('appImage', TextType::class, [
				'label' 		=> false
			])
			->add('enabled', ChoiceType::class, [
				'choices' 		=> [
					'yes' 		=> true,
					'no' 		=> false
				],
				'label' 		=> false
			])
			->add('group', EntityType::class, [
				'class'			=> 'AppBundle:ServiceGroupsDB',
				'choice_label'	=> 'name',
				'multiple'		=> false,
				'required'		=> false,
				'label'			=> false
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
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\ServicesDB']);
	}

}