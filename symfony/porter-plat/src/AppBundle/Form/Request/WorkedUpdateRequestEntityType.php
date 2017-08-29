<?php

// src/AppBundle/Form/Request/WorkedUpdateRequestEntityType.php
namespace AppBundle\Form\Request;

use AppBundle\Services\RequestOperations;
use AppBundle\Services\Database;
use AppBundle\Form\Request\PorterRequestActivitySummaryUpdateType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

use \DateTime;
use \DateTimeZone;

class WorkedUpdateRequestEntityType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder_, array $options) {
		$builder_
			->add('rId', HiddenType::class, [
				'label' 		=> false
			])
			->add('rDiscount', EntityType::class, [
				'class'			=> 'AppBundle:DiscountsDB',
				'choice_label'	=> 'name',
				'multiple'		=> false,
				'required'		=> false,
				'label'			=> false
			])
			->add('porterSummaries', CollectionType::class, [
				'allow_add'		=> true,
        		'by_reference'	=> false,
        		'label'			=> false,
				'entry_type'	=> PorterRequestActivitySummaryUpdateType::class
			]);
			
		$builder_->get('rId')
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
		$resolver_->setDefaults(['data_class' => 'AppBundle\Entity\WorkedUpdateRequestEntity']);
	}

}