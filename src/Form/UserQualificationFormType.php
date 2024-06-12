<?php

namespace App\Form;

use App\Entity\Qualification;
use App\Entity\User;
use App\Entity\UserQualification;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
            ])
            ->add('qualification', EntityType::class, [
                'label' => 'Kwalifikacje',
                'class' => Qualification::class,
                'choice_label' => function($value) {
                    return $value->getName();
                },
                'choice_value' => 'id'
            ])
            ->add('fileName', FileType::class, [
                'data_class'=>null,
                'label' => 'Możesz dodać plik typu obraz lub pdf.',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'image/*',
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Ten format pliku nie jest obsługiwany.'
                    ])
                ]
            ])
            ->add('edit', SubmitType::class, ['label' => 'Popraw', 'attr' => ['class' => 'btn btn-primary mt-3']])
            ->add('remove', SubmitType::class, ['label' => 'Usuń', 'attr' => ['class' => 'btn btn-danger mt-3']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserQualification::class,
        ]);
    }
}
