<?php

// src/AppBundle/Form/ServiceGroup/ServiceGroupType.php
namespace AppBundle\Form\ServiceGroup;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceGroupType extends AbstractType {

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
			->add('webImage', UrlType::class, [
				'label' 		=> false
			])
			->add('appImage', UrlType::class, [
				'label' 		=> false
			])
			->add('enabled', ChoiceType::class, [
				'choices' 		=> [
					'yes' 		=> true,
					'no' 		=> false
				],
				'label' 		=> false
			]);
			/*->add('services', HiddenType::class, [
				'label' 		=> false
			]);*/

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
		/*$builder_->get('services')
			->addModelTransformer(new CallbackTransformer(
				function ($toFront_) {
					if ($toFront_ !== null) {
						$output = '';
						foreach ($toFront_ as $service)
							$output .= $service->getName() . '<br>';
						return $output;
					}
					return '';
				},
				function ($fromFront_) {
					return null;
				}
			));*/
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\ServiceGroupsDB']);
	}

}