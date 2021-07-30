<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\College;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('college', EntityType::class, [
                'class' => College::class,
                'choice_label' => 'name',
                'label' => 'Campus',
            ])
            ->add('toSearch', TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'required' => false,
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Entre ',
                'html5' => true,
                'required' => false,
                'widget' => 'single_text',
                'empty_data' => '',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'et ',
                'html5' => true,
                'required' => false,
                'widget' => 'single_text',
                'empty_data' => '',
            ])
            ->add('isOwner', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur',
                'required' => false,
            ])
            ->add('ameIInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit',
                'required' => false,
            ])
            ->add('ameIUninscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit',
                'required' => false,
            ])
            ->add('journeysPassed', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ]);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
}