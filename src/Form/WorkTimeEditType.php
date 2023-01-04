<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\WorkTime;
use App\Entity\Employ;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkTimeEditType extends AbstractType
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
            ->add('work_start', TimeType::class, [
                'label' => 'Godzina rozpoczęcia pracy',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('work_end', TimeType::class, [
                'label' => 'Godzina zakończenia pracy',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('travel_time', TimeType::class, [
                'label' => 'Czas dojazdu',
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
            ->add('employ', EntityType::class, [
                'label' => 'Sposób zatrudnienia',
                'class' => Employ::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'query_builder' => function(EntityRepository $repo) {
                    $builder = $repo->createQueryBuilder('employ');
                    return $builder->where('employ.active = 1');
                },
            ])
            ->add('project', EntityType::class, [
                'label' => 'Projekt',
                'class' => Project::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'query_builder' => function(EntityRepository $repo) {
                    $builder = $repo->createQueryBuilder('project');
                    return $builder->where('project.active = 1');
                },
            ])
            ->add('cost_hour', MoneyType::class, [
                'label' => 'Stawka godzinowa',
                'currency' => 'PLN',
                'scale' => 2,
                'required' => false
            ])
            ->add('edit', SubmitType::class, ['label' => 'Popraw', 'attr' => ['class' => 'btn btn-primary mt-3']])
            ->add('remove', SubmitType::class, ['label' => 'Usuń', 'attr' => ['class' => 'btn btn-danger mt-3']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkTime::class,
        ]);
    }
}
