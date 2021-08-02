<?php

namespace App\Form;

use App\Entity\Journeys;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancelJourneyAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('startingDate')
            ->add('duration')
            ->add('deadlineDate')
            ->add('nbInscriptionMax')
            ->add('description')
            ->add('college')
            ->add('users')
            ->add('user')
            ->add('status')
            ->add('place')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Journeys::class,
        ]);
    }
}
