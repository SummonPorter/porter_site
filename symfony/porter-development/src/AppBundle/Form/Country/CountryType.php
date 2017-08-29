<?php

// src/AppBundle/Form/Country/CountryType.php
namespace AppBundle\Form\Country;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class CountryType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 	=> false,
				'attr'		=> ['readonly' => true]
			])
			->add('name', TextType::class, [
				'label' 	=> false
			])
			->add('shortName', TextType::class, [
				'label' 	=> false
			])
			->add('currencyCode', TextType::class, [
				'label' 	=> false
			])
			->add('enabled', ChoiceType::class, [
				'choices' 	=> [
					'yes' 	=> true,
					'no' 	=> false
				],
				'label' 	=> false
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\CountriesDB']);
	}

}