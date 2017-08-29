<?php

// src/AppBundle/Form/City/CityType.php
namespace AppBundle\Form\City;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

use \DateTimeZone;

class CityType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('name', TextType::class, [
				'label' 		=> false
			])
			->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event_) {
				
				$choices = [];
				$form = $event_->getForm();

		        $city = $event_->getData();

		        if ($city !== null) {
		        	if ($city->getCountry() !== null)
			        	$timezones = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $city->getCountry()->getShortName());
			        else
			        	$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
			        foreach ($timezones as $timezone)
			        	$choices[$timezone] = $timezone;
			    }

				$form->add('timezone', ChoiceType::class, [
            		'label'			=> false,
            		'required'		=> false,
            		'choices'		=> $choices
				]);
			})
			->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event_) {
				
				$choices = [];
				$form = $event_->getForm();

		        $submitData = $event_->getData();
		        
		        //	HACK: passing in passed value to solve validation
		        if ($submitData !== null)
			        $choices[$submitData['timezone']] = $submitData['timezone'];

				$form->add('timezone', ChoiceType::class, [
            		'label'			=> false,
            		'required'		=> false,
            		'choices'		=> $choices
				]);
			})
			->add('latitude', NumberType::class, [
				'label'			=> false
			])
			->add('longitude', NumberType::class, [
				'label'			=> false
			])
			->add('enabled', ChoiceType::class, [
				'choices' 		=> [
					'yes' 		=> true,
					'no' 		=> false
				],
				'label' 		=> false
			])
			->add('country', EntityType::class, [
				'class'			=> 'AppBundle:CountriesDB',
				'choice_label'	=> 'name',
				'label'			=> false
			])
			->add('region', EntityType::class, [
				'class'			=> 'AppBundle:RegionsDB',
				'choice_label'	=> 'name',
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\CitiesDB']);
	}

}