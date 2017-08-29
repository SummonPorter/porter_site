<?php

// src/AppBundle/Form/CheckedEntitiesDeleteListType.php
namespace AppBundle\Form;

use AppBundle\Form\CheckedEntityType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckedEntitiesDeleteListType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options_) {
		$builder_
			->add('entities', CollectionType::class, [
            	'entry_type'	=> CheckedEntityType::class,
            	'allow_add'		=> true,
            	'by_reference'	=> false,
            	'label'			=> false
            ])
            ->add('listFunction', HiddenType::class, [
				'label' 		=> false
			])
			->add('delete', SubmitType::class, [
				'label'			=> 'Delete'
			]);
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\EntitiesList']);
	}

}