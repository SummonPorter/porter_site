<?php

// src/AppBundle/Form/Request/NewRequestEntityType.php
namespace AppBundle\Form\Request;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

use \DateTime;
use \DateTimeZone;

class NewRequestEntityType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('cEmail', EmailType::class, [
				'label' 		=> false
			])
			->add('cFirstName', TextType::class, [
				'label' 		=> false
			])
			->add('cLastName', TextType::class, [
				'label' 		=> false
			])
			->add('cMobilePhone', TextType::class, [
				'label' 		=> false
			])
			->add('cHomePhone', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('cCity', EntityType::class, [
				'class'			=> 'AppBundle:CitiesDB',
				'choice_label'	=> 'nameAndDetails',
				'multiple'		=> false,
				'required'		=> false,
				'label'			=> false
			])
			->add('cAddress', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('cMailing', TextType::class, [
				'label' 		=> false,
				'required'		=> false
			])
			->add('ccNum', TextType::class, [
				'label' 		=> false,
			])
			->add('ccName', TextType::class, [
				'label' 		=> false,
			])
			->add('ccExp', DateType::class, [
				'label' 		=> false
			])
			->add('ccCode', TextType::class, [
				'label' 		=> false,
			])
			->add('rService', EntityType::class, [
				'class'			=> 'AppBundle:ServicesDB',
				'choice_label'	=> 'name',
				'multiple'		=> false,
				'label'			=> false
			])
			->add('rDiscount', EntityType::class, [
				'class'			=> 'AppBundle:DiscountsDB',
				'choice_label'	=> 'name',
				'multiple'		=> false,
				'required'		=> false,
				'label'			=> false
			])
			->add('rPortersWanted', IntegerType::class, [
				'label' 		=> false
			])
			->add('rStartDateTime', DateTimeType::class, [
				'label' 		=> false,
				'format'		=> 'yyyyMMddhh:mm:ss'
			])
			->add('rEstDuration', IntegerType::class, [
				'label' 		=> false
			])
			->add('rDetails', TextareaType::class, [
				'label' 		=> false
			]);
	}

	public function configureOptions(OptionsResolver $resolver_) {
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\NewRequestEntity']);
	}

}