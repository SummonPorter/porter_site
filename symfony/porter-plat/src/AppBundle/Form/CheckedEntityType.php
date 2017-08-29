<?php

// src/AppBundle/Form/CheckedEntityType.php
namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckedEntityType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('checked', CheckboxType::class, [
            	'label' 		=> false,
            	'required'		=> false
            ])
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('doctrinePath', HiddenType::class, [
				'label' 		=> false
			])
			->add('entityClass', HiddenType::class, [
				'label' 		=> false
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\CheckedEntity']);
	}

}