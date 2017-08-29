<?php

// src/AppBundle/Form/Request/RequestAddressType.php
namespace AppBundle\Form\Request;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestAddressType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options_) {
		$builder_
			->add('id', HiddenType::class, [
				'label' 		=> false
			])
			->add('city', EntityType::class, [
				'class'			=> 'AppBundle:CitiesDB',
				'choice_label'	=> 'nameAndDetails',
				'multiple'		=> false,
				'label'			=> false
			])
			->add('address', TextType::class, [
				'label' 		=> false
			])
			->add('mailing', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('unit', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('context', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('stepping', IntegerType::class, [
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\RequestAddressesDB']);
	}

}