<?php

namespace App\Form;

use App\Entity\Journeys;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditJourneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('startingDate', DateTimeType::class, [
                'label' => 'Date et heure de la sortie : ',
                'html5' => true,
                'required' => false,
                'widget' => 'single_text',
                'empty_data' => '',
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (en minutes)',
            ])
            ->add('deadlineDate', DateType::class, [
                'label' => 'Date limite d\'inscription ',
                'html5' => true,
                'required' => false,
                'widget' => 'single_text',
                'empty_data' => '',
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de place'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description et infos',
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'label' => 'Lieu : ',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Journeys::class,
        ]);
    }
}
