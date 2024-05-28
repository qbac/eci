<?php

namespace App\Form;

use App\Entity\Qualification;
use App\Entity\User;
use App\Entity\UserQualification;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserQualificationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_start', DateType::class, [
                'label' => 'Data',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
            ])
            ->add('date_end', DateType::class, [
                'label' => 'Data',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
            ])
            ->add('type', TextareaType::class, [
                'label' => 'Typ',
                'required' => false
            ])
            ->add('link', TextareaType::class, [
                'label' => 'Link',
                'required' => false
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktywny',
                'required' => false,
                'mapped' => false
            ])
            ->add('qualification', EntityType::class, [
                'label' => 'Kwalifikacje',
                'class' => Qualification::class,
                'choice_label' => function($value) {
                    return $value->getName();
                },
                'choice_value' => 'id'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserQualification::class,
        ]);
    }
}
