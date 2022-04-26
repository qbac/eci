<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\WorkTime;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('work_date', DateType::class, [
                'label' => 'Data',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
            ])
            ->add('work_time', TimeType::class, [
                'label' => 'Ilość przepracowanych godzin',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('user', EntityType::class, [
                'label' => 'Pracownik',
                'class' => User::class,
                'choice_label' => function($value) {
                    return $value->getFirstName().' '.$value->getlastName();
                },
                'choice_value' => 'id'

            ])
            ->add('project', EntityType::class, [
                'label' => 'Projekt',
                'class' => Project::class,
                'choice_label' => 'name',
                'choice_value' => 'id'
            ])
        ;

        $builder->get('work_date')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if(!$value) {
                    return new \DateTime('now - 1 day');
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));

        $builder->get('work_time')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if(!$value) {
                    return new \DateTime('08:00:00');
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkTime::class,
        ]);
    }
}
