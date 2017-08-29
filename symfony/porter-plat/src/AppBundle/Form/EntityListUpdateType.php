<?php

// src/AppBundle/Form/EntityListUpdateType.php
namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityListUpdateType extends AbstractType {

	protected $entryType;

	public function __construct($entryType_) {
		$this->entryType = $entryType_;
	}

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('entities', CollectionType::class, [
            	'entry_type'	=> $this->entryType,
            	'allow_add'		=> true,
            	'by_reference'	=> false,
            	'label'			=> false
            ])
            ->add('listFunction', HiddenType::class, [
				'label' 		=> false
			])
			->add('save', SubmitType::class, [
				'label'			=> 'Save'
			]);
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\EntitiesList']);
	}

}