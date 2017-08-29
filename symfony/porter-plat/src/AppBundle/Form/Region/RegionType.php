<?php

// src/AppBundle/Form/Region/RegionType.php
namespace AppBundle\Form\Region;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class RegionType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options_) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('typeName', ChoiceType::class, [
				'choices' 		=> [
					'province' 	=> 'province',
					'state' 	=> 'state',
					'other'		=>	'other'
				],
				'label' 		=> false
			])
			->add('name', TextType::class, [
				'label' 		=> false
			])
			->add('shortName', TextType::class, [
				'label'			=> false
			])
			->add('country', EntityType::class, [
				'class'			=> 'AppBundle:CountriesDB',
				'choice_label'	=> 'name',
				'label'			=> false
			])
			->add('requestTax', NumberType::class, [
				'label'			=> false
			])
			->add('porterTax', NumberType::class, [
				'label'			=> false
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\RegionsDB']);
	}

}